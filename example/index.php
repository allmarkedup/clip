<?php

require __DIR__ .'/../vendor/autoload.php';

use Amu\Clip\Console;
use Amu\Clip\Commands\ListCommand;
use Amu\Clip\Commands\HelpCommand;

$console = new Console();

$console->addCommand(new ListCommand());
$console->addCommand(new HelpCommand());

$console->run();