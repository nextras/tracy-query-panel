<?php

namespace Nextras\TracyQueryPanel\DI;

use Nette;
use Nette\DI\CompilerExtension;
use Tracy\Debugger;


class QueryPanelExtension extends CompilerExtension
{

	public function loadConfiguration()
	{
		if (!class_exists('Tracy\Debugger') || Debugger::$productionMode === TRUE)
		{
			return;
		}

		$config = $this->getConfig();

		$builder = $this->getContainerBuilder();
		$def = $builder
			->addDefinition($this->prefix('panel'))
			->setClass('Nextras\TracyQueryPanel\QueryPanel');

		$builder->getDefinition('tracy.bar')
			->addSetup('addPanel', ['@' . $this->prefix('panel')]);

		foreach ($config as $class)
		{
			$def->addSetup("$class::register");
		}
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
