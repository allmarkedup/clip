<?php namespace Amu\Clip;

use Amu\Clip\Exception\Exception;
use Amu\Clip\Commands\Command;
use Amu\Clip\Commands\ListCommand;

class Console
{
    protected $commands = [];

    protected $defaultCommand = 'list';

    private $outputHandler;

    public function __construct()
    {
        $this->addCommand(new ListCommand());
    }

    public function setOutputHandler(Output $output)
    {
        $this->outputHandler = $output;
    }

    public function getOutputHandler()
    {
        if ( ! $this->outputHandler ) {
            $this->outputHandler = new Output();
        }
        return $this->outputHandler;
    }

    public function addCommand(Command $command)
    {
        $command->setConsole($this);
        $this->commands[$command->getName()] = $command;
    }

    public function getCommand($name)
    {
        if (isset($this->commands[$name])) {
            return $this->commands[$name];
        }
        throw new Exception('Command not found');
    }

    public function getCommands()
    {
        return $this->commands;
    }

    public function run(array $argv = null)
    {
        $output = $this->getOutputHandler();
        try {
            if ($argv === null) {
                $argv = isset($_SERVER['argv']) ? array_slice($_SERVER['argv'], 1) : array();
            }
            if (count($argv) === 0 || strpos($argv[0], '-') === 0) {
                $command = $this->getCommand($this->defaultCommand);
            } else {
                $command = $this->getCommand($argv[0]);
                array_shift($argv);
            }
            $input = new Input($argv);
            $input->parseAndValidate($command->getSignature());
            $command->run($input, $output);
        } catch (\Exception $e) {
            $output->error($e->getMessage());
        }
    }
}
