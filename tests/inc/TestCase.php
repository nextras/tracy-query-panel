<?php

namespace NextrasTests\TracyQueryPanel;

use Mockery;
use Nette;
use Nextras\TracyQueryPanel\DI\QueryPanelExtension;
use Tracy\Debugger;


class TestCase extends \Tester\TestCase
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
		$config->addConfig(__DIR__ . '/config.neon');
		return $config->createContainer();
	}

	protected function tearDown()
	{
		parent::tearDown();
		Mockery::close();
	}

}
