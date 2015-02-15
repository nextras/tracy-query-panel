<?php

/** @testCase */

namespace NextrasTests\TracyQueryPanel;

use Mockery;
use Nextras\TracyQueryPanel\QueryCollector;
use Nextras\TracyQueryPanel\QueryPanel;
use Nette;
use Tester\Assert;
use Tracy;


require_once __DIR__ . '/../../bootstrap.php';


class QueryPanelTest extends TestCase
{

	public function testAddQuery()
	{
		/** @var Tracy\Bar|Mockery\MockInterface $bar */
		$bar = Mockery::mock('Tracy\\Bar');
		$bar->shouldReceive('addPanel')->andReturnNull();
		$panel = new QueryPanel($bar);

		$query = Mockery::mock('Nextras\\TracyQueryPanel\\IVoidQuery');
		$panel->addQuery($query);

		$access = Access($panel, '$collector');
		/** @var QueryCollector $qc */
		$qc = $access->get();
		Assert::same([$query], $qc->getQueries());
	}

}

$test = new QueryPanelTest();
$test->run();
