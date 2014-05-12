<?php

$loader = require __DIR__ . '/../vendor/autoload.php';
$loader->add('Tracy\\QueryPanel', __DIR__ . '/../src');
require __DIR__ . '/MockQuery.php';

use Tracy\Debugger;
Debugger::enable();

$panel = new \Tracy\QueryPanel\QueryPanel();
for ($i = 0; $i < 15; ++$i)
{
	$panel->addQuery(new MockQuery());
}

$bar = Debugger::getBar();
$bar->addPanel($panel);
