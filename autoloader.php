<?php
require_once __DIR__.'/src/Tco/Autoloader/Psr4AutoloaderClass.php';

$loader = new Psr4AutoloaderClass();
$loader->register();

$loader->addNamespace('Tco', __DIR__ . '/src/Tco' );
$loader->addNamespace('Tco\Examples', __DIR__ . '/Examples' );
