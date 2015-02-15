<?php

namespace Nextras\TracyQueryPanel\Bridges\NetteDI;

use Nette;
use Nette\DI\CompilerExtension;
use Tracy\Debugger;


class QueryPanelExtension extends CompilerExtension
{

	public function loadConfiguration()
	{
		if (class_exists('Tracy\Debugger') && Debugger::$productionMode === FALSE)
		{
			return;
		}

		$this->getContainerBuilder()
			->addDefinition($this->prefix('panel'))
			->setClass('Nextras\TracyQueryPanel\QueryPanel');
	}


	/**
	 * @param Nette\Configurator $configurator
	 */
	public static function register(Nette\Configurator $configurator)
	{
		$configurator->onCompile[] = function ($_, Nette\DI\Compiler $compiler) {
			$compiler->addExtension('queryPanel', new QueryPanelExtension());
		};
	}

}
