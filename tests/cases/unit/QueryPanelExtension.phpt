<?php

/** @testCase */

namespace NextrasTests\TracyQueryPanel;

use Mockery;
use Nextras\TracyQueryPanel\QueryPanel;
use Nette;
use Tester\Assert;


require_once __DIR__ . '/../../bootstrap.php';


class QueryPanelExtensionTest extends TestCase
{

	public function testRegisteredOnDebug()
	{
		$dic = $this->createContainer(TRUE);
		Assert::true($dic->getService('queryPanel.panel') instanceof QueryPanel);

		$connection = $dic->getService('nette.connection');
		Assert::count(1, $connection->onQuery);
	}

	public function testSkippedOnDebug()
	{
		$dic = $this->createContainer(FALSE);
		Assert::exception(function() use ($dic) {
			$dic->getService('queryPanel.panel');
		}, 'Nette\DI\MissingServiceException');
	}

}

$test = new QueryPanelExtensionTest();
$test->run();
