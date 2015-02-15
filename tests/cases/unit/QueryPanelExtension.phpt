<?php

/** @testCase */

namespace NextrasTests\TracyQueryPanel;

use Mockery;
use Nextras\TracyQueryPanel\Bridges\NetteDI\QueryPanelExtension;
use Nextras\TracyQueryPanel\QueryPanel;
use Nette;
use Tester\Assert;
use Tracy\Debugger;


require_once __DIR__ . '/../../bootstrap.php';


class QueryPanelExtensionTest extends TestCase
{

	/**
	 * @param bool $debugMode
	 * @return Nette\DI\Container
	 */
	protected function createContainer($debugMode)
	{
		$config = new Nette\Configurator();
		$config->setTempDirectory(TEMP_DIR);
		$config->setDebugMode($debugMode);
		Debugger::$productionMode = !$debugMode;
		QueryPanelExtension::register($config);
		return $config->createContainer();
	}

	public function testRegisteredOnDebug()
	{
		$dic = $this->createContainer(TRUE);
		Assert::true($dic->getService('queryPanel.panel') instanceof QueryPanel);
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
