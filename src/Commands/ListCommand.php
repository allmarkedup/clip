<?php namespace Amu\Clip\Commands;

use Amu\Clip\Input;
use Amu\Clip\Output;

/**
 * @command list
 * @description List all available commands
 */
class ListCommand extends BaseCommand
{
    public function execute(Input $input, Output $output)
    {
        $commands = $this->getConsole()->getCommands();
        $output->br();
        foreach ($commands as $command) {
            $output->padding(20)
                    ->label($command->getName())
                    ->result($command->getDescription());
        }
        $output->br();
    }
}
