<?php

/** @testCase */

namespace NextrasTests\TracyQueryPanel\Queries;

use Mockery;
use Nextras\TracyQueryPanel\Queries\NetteDatabaseQuery;
use Nextras\TracyQueryPanel\QueryCollector;
use Nextras\TracyQueryPanel\QueryPanel;
use Nette;
use NextrasTests\TracyQueryPanel\TestCase;
use Tester\Assert;
use Tracy;


require_once __DIR__ . '/../../bootstrap.php';


class NetteDatabaseQueryTest extends TestCase
{

	const DRIVER = 'pgsql';
	const DBNAME = 'foobar';
	const ELAPSED = 1337;

	public function testSelect()
	{
		$bar = Mockery::mock('Tracy\\Bar');
		$bar->shouldReceive('addPanel')
			->andReturnSelf();
		$panel = new QueryPanel($bar);

		$driver = 'pgsql';
		$dbname = 'foobar';

		$connection = new \NDBConnectionMock(self::DRIVER . ':user=guest;dbname=' . self::DBNAME . ';host=localhost');
		NetteDatabaseQuery::register($panel, $connection);

		$access = Access($panel, '$collector');
		/** @var QueryCollector $qc */
		$qc = $access->get();

		$connection->query('SELECT 1');

		$queries = $qc->getQueries();
		Assert::count(1, $queries);

		/** @var NetteDatabaseQuery $query */
		$query = array_pop($queries);
		Assert::true($query instanceof NetteDatabaseQuery);

		Assert::same(self::DRIVER, $query->getStorageType());
		Assert::same(self::DBNAME, $query->getDatabaseName());
		Assert::same(self::ELAPSED, $query->getElapsedTime());
		Assert::same('<pre class="dump"><strong style="color:blue">SELECT</strong> 1</pre>', $query->getQuery());
		Assert::true(strpos($query->getResult(), '<table') !== FALSE);
	}

}

$test = new NetteDatabaseQueryTest();
$test->run();
