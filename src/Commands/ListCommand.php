<?php namespace Amu\Clip\Commands;

use Amu\Clip\Input;
use Amu\Clip\Output;
use Amu\Clip\Command;

/**
 * @command list
 * @description List all available commands
 */
class ListCommand extends Command
{
    public function execute(Input $input, Output $output)
    {
        $commands = $this->getConsole()->getCommands();
        foreach($commands as $command){
            echo $command->getName() . "\n";
        }
    }
}
