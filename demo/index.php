<?php

$loader = require __DIR__ . '/../vendor/autoload.php';
$loader->add('Tracy\\QueryPanel', __DIR__ . '/../src');
require __DIR__ . '/MockQuery.php';

use Tracy\Debugger;
Debugger::enable();

$panel = new \Tracy\QueryPanel\QueryPanel();

$nums = [0, 1, 5, 100];
$nums = [100];
$num = $nums[array_rand($nums)];
for ($i = 0; $i < $num; ++$i)
{
	$panel->addQuery(new MockQuery());
}

$bar = Debugger::getBar();
$bar->addPanel($panel);
