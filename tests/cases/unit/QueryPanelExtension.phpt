<?php

/** @testCase */

namespace NextrasTests\TracyQueryPanel;

use Mockery;
use Nextras\TracyQueryPanel\Bridges\NetteDI\QueryPanelExtension;
use Nextras\TracyQueryPanel\QueryPanel;
use Nette;
use Tester\Assert;


require_once __DIR__ . '/../../bootstrap.php';


class QueryPanelExtensionTest extends TestCase
{

	/**
	 * @return Nette\DI\Container
	 */
	protected function createContainer()
	{
		$config = new Nette\Configurator();
		$config->setTempDirectory(TEMP_DIR);
		QueryPanelExtension::register($config);
		return $config->createContainer();
	}

	public function testFunctional()
	{
		$dic = $this->createContainer();
		Assert::true($dic->getService('queryPanel.panel') instanceof QueryPanel);
	}

}

$test = new QueryPanelExtensionTest();
$test->run();
