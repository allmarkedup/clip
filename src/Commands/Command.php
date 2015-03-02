<?php namespace Amu\Clip\Commands;

use Amu\Clip\Input;
use Amu\Clip\Output;
use Amu\Clip\Console;
use Amu\Clip\Reflection\CommandClass;

abstract class Command
{
    private $_console;

    private $_reflectedClass;

    private $_reflectedProperties;

    private $signature;

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
        if (! $this->signature) {
            $this->signature = [
                'args' => $this->getArgumentSignature(),
                'opts' => $this->getOptionSignature(),
            ];
        }

        return $this->signature;
    }

    protected function getArgumentSignature()
    {
        $signature = [];
        foreach ($this->getCommandClass()->getArgumentProperties() as $prop) {
            $signature[] = [
                'key' => $prop->getPropertyName(),
                'name' => $prop->getName(),
                'validate' => $prop->getValidationRules(),
                'required' => $prop->isRequired(),
                'description' => $prop->getDescription(),
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
                'description' => $prop->getDescription(),
            ];
        }

        return $signature;
    }

    public function run(Input $input, Output $output)
    {
        $this->setPropertiesFromInput($input);
        if (! empty($this->help)) {
            return $this->help($input, $output);
        }
        if ($input->hasErrors()) {
            return $this->printErrors($input->getErrors(), $output);
        }

        return $this->execute($input, $output);
    }

    protected function help(Input $input, Output $output)
    {
        $sig = $this->getSignature();
        $args = implode(' ', array_map(function ($arg) {
            $name = '<'.$arg['name'].'>';

            return $arg['required'] ? $name : '['.$name.']';
        }, $sig['args']));

        $output->br()->yellow('Usage: php '.$_SERVER['PHP_SELF'].' '.$this->getName().' '.$args);

        if ($desc = $this->getDescription()) {
            $output->br()->out($desc);
        }

        if (count($sig['opts'])) {
            $output->br();
            $optStrings = [];
            foreach ($sig['opts'] as $opt) {
                $long = $opt['long'] ? '--'.$opt['long'] : null;
                $short = $opt['short'] ? '-'.$opt['short'] : null;
                $optStrings[] = [
                    'label' => '  '.implode(', ', array_filter([$long, $short], 'strlen')).' ',
                    'description' => $opt['description'],
                ];
            }
            $maxOptsLen = 0;
            foreach ($optStrings as $os) {
                if (mb_strlen($os['label']) > $maxOptsLen) {
                    $maxOptsLen = mb_strlen($os['label']);
                }
            }
            $pad = $output->padding($maxOptsLen + 5, ' ');
            foreach ($optStrings as $os) {
                $pad->label($os['label'])->result($os['description']);
            }
        }

        $output->br();
    }

    protected function printErrors($errors, $output)
    {
        foreach ($errors as $error) {
            $output->error($error->getMessage());
        }
    }

    protected function setPropertiesFromInput(Input $input)
    {
        foreach ($input->getAll() as $key => $result) {
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
