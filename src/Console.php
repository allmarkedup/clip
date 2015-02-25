<?php namespace Amu\Clip;

class Console
{
    protected $commands = [];

    public function addCommand(Command $command)
    {
        $command->setConsole($this);
        $this->commands[$command->getName()] = $command;
    }

    public function getCommands()
    {
        return $this->commands;
    }

    public function run(array $argv = null)
    {
        $command = 'list'; // test
        $this->getCommands()[$command]->execute(new Input(), new Output());
    }
}
