<?php namespace Amu\Clip;

use Amu\Clip\Exception\Exception;
use Amu\Clip\Commands\Command;
use Amu\Clip\Commands\ListCommand;

class Console
{
    protected $name;

    protected $version;

    protected $commands = [];

    protected $defaultCommand = 'list';

    private $outputHandler;

    public function __construct($name = null, $version = null)
    {
        $this->name = $name;
        $this->version = $version;
        $this->addCommand(new ListCommand());
    }

    public function getName()
    {
        return $this->name ?: 'Console Application';
    }

    public function getVersion()
    {
        return $this->version;
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
            return $command->run($input, $output);
        } catch (\Exception $e) {
            $output->error($e->getMessage());
        }
    }

    public function call($commandName, $args = [])
    {
        $output = $this->getOutputHandler();
        try {            
            $command = $this->getCommand($commandName);
            $signature = $command->getSignature();

            $indexedArgKeys = array_map(function($arg){
                return $arg['key'];
            }, $signature['args']);

            // create $argv style array from args
            $argvOpts = [];
            $argvArgs = [];
            foreach($args as $key => $value) {
                if (strpos($key, '-') === 0) {
                    if ($value !== false) {
                        if ( strpos($key, '--') === 0) {
                            if ($value === true) {
                                $argvArgs[] = $key;
                            } else {
                                $argvArgs[] = $key . '=' . $value;    
                            }
                        } else {
                            if ($value === true) {
                                $argvArgs[] = $key;
                            } else {
                                $argvArgs[] = $key;
                                $argvArgs[] = $value;
                            }
                        }
                    }
                } else {
                    $index = array_search($key, $indexedArgKeys);
                    if ($index !== false) {
                        $argvOpts[$index] = $value;
                    }
                }
            }

            $input = new Input(array_merge($argvOpts, $argvArgs));
            $input->parseAndValidate($signature);
            return $command->run($input, $output);
        } catch (\Exception $e) {
            $output->error($e->getMessage());
        }   
    }

}
