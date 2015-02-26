<?php

require __DIR__ .'/../vendor/autoload.php';

use Amu\Clip\Console;
use Amu\Clip\Commands\ListCommand;
use Amu\Clip\Commands\HelpCommand;
use Amu\Clip\Commands\SayHelloCommand;

$console = new Console();

$console->addCommand(new HelpCommand());
$console->addCommand(new SayHelloCommand());

$console->run();