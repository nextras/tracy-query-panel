<?php

if (@!include __DIR__ . '/../../vendor/autoload.php') {
	echo "Install Nette Tester using `composer update`\n";
	exit(1);
}


$setupMode = TRUE;

echo "[setup] Purging temp.\n";
@mkdir(__DIR__ . '/../temp');
Tester\Helpers::purge(__DIR__ . '/../temp');

echo "[setup] All done.\n\n";
