<?php

require __DIR__ .'/../vendor/autoload.php';

use Amu\Clip\Console;
use Amu\Clip\Commands\HelpCommand;

$console = new Console();

$console->addCommand('HelpCommand');