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

	public function testSelect()
	{
		$dic = $this->createContainer(TRUE);
		/** @var QueryPanel $panel */
		$panel = $dic->getService('queryPanel.panel');
		/** @var Nette\Database\Connection $connection */
		$connection = $dic->getService('nette.connection');
dump($connection);
		$access = Access($panel, '$collector');
		/** @var QueryCollector $qc */
		$qc = $access->get();

		$connection->query('SELECT 1');

		$queries = $qc->getQueries();
		Assert::count(1, $queries);

		/** @var NetteDatabaseQuery $query */
		$query = array_pop($queries);
		Assert::true($query instanceof NetteDatabaseQuery);

		Assert::same('pgsql', $query->getStorageType()); // defined in config.neon
		Assert::same('foobar', $query->getDatabaseName()); // -//-
		Assert::same(1337, $query->getElapsedTime()); // defined in NDBConnectionMock
		Assert::same('<pre class="dump"><strong style="color:blue">SELECT</strong> 1</pre>', $query->getQuery());
		Assert::true(strpos($query->getResult(), '<table') !== FALSE);
	}

}

$test = new NetteDatabaseQueryTest();
$test->run();
