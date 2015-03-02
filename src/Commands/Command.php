<?php namespace Amu\Clip\Commands;

use Amu\Clip\Input;
use Amu\Clip\Output;
use Amu\Clip\Console;
use Amu\Clip\Reflection\CommandClass;
use Amu\Clip\Reflection\CommandProperty;
use Amu\Clip\Exception\AnnotationNotFoundException as NotFoundException;

abstract class Command
{
    private $_console;

    private $_reflectedClass;

    private $_reflectedProperties;

    abstract public function execute(Input $input, Output $output);

    public function getConsole()
    {
        return $this->_console;
    }

    public function setConsole(Console $console)
    {
        $this->_console = $console;
    }

    public function getName()
    {
        return $this->getCommandClass()->getName();
    }

    public function getDescription()
    {
        return $this->getCommandClass()->getDescription();
    }

    public function getSignature()
    {
        return [
            'args' => $this->getArgumentSignature(),
            'opts' => $this->getOptionSignature()
        ];
    }

    protected function getArgumentSignature()
    {
        $signature = [];
        foreach ($this->getCommandClass()->getArgumentProperties() as $prop) {
            $signature[] = [
                'key' => $prop->getPropertyName(),
                'name' => $prop->getName(),
                'validate' => $prop->getValidationRules()
            ];
        }
        return $signature;
    }

    protected function getOptionSignature()
    {
        $signature = [];
        foreach ($this->getCommandClass()->getOptionProperties() as $prop) {
            $signature[] = [
                'key' => $prop->getPropertyName(),
                'long' => $prop->getLongName(),
                'short' => $prop->getShortName(),
                'valueType' => $prop->getInputValueType(),
                'validate' => $prop->getValidationRules(),
            ];
        }
        return $signature;
    }

    public function run(Input $input, Output $output)
    {
        $this->setPropertiesFromInput($input);
        if ( ! empty($this->help)) {
            return $this->help($input, $output);
        }
        if ($input->hasErrors()) {
            return $this->printErrors($input->getErrors(), $output);
        }
        return $this->execute($input, $output);
    }

    protected function help(Input $input, Output $output)
    {
        $input->getOpts();
    }

    protected function printErrors($errors, $output)
    {
        foreach ($errors as $error) {
            $output->error($error->getMessage());
        }
    }

    protected function setPropertiesFromInput(Input $input)
    {
        foreach($input->getAll() as $key => $result){
            $this->$key = $result;
        }
    }

    private function getCommandClass()
    {
        if (! $this->_reflectedClass) {
            $this->_reflectedClass = new CommandClass($this);
        }
        return $this->_reflectedClass;
    }
}
