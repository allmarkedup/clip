<?php namespace Amu\Clip\Commands;

use Amu\Clip\Input;
use Amu\Clip\Output;
use Amu\Clip\Console;
use Amu\Clip\Annotations\ClassParser;

abstract class Command
{
    private $_console;

    private $_annotationParser;

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
        return $this->getClassAnnotation('command') ?: $this->getNameFromClassName();
    }

    public function getDescription()
    {
        return $this->getClassAnnotation('description') ?: '';
    }

    public function getSignature()
    {
        return [
            'args' => $this->getArguments(),
            'opts' => $this->getArguments()
        ];
    }

    public function run(Input $input, Output $output)
    {
        // TODO set property values here
        return $this->execute($input, $output);
    }

    protected function getArguments()
    {
        return $this->filterProperties('type', 'argument');
    }

    protected function getOptions()
    {
        return $this->filterProperties('type', 'option');
    }

    protected function filterProperties($key, $value)
    {
        return array_filter($this->getAnnotatedProperties(), function($property) use ($key, $value) {
            return isset($property['annotations'][$key]) && $property['annotations'][$key] === $value;
        });
    }

    protected function getAnnotatedProperties()
    {
        return array_map(function($property){
            if ( isset($property['annotations']['validate'] ) ) {
                $property['annotations']['validate'] = $this->parseValidationRules($property['annotations']['validate']);
            }
            return $property;
        }, $this->_annotationParser->getAnnotatedProperties());
    }

    protected function parseValidationRules($validationString)
    {
        return explode('|', $validationString);
    }

    protected function getNameFromClassName()
    {
        $className = implode('', array_slice(explode('\\', get_class($this)), -1));
        $className = str_replace('Command', '', $className);
        preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $className, $matches);
        $ret = $matches[0];
        foreach ($ret as &$match) {
            $match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
        }

        return implode('-', $ret);
    }

    private function getClassAnnotation($name)
    {
        if (! $this->_annotationParser) {
            $this->_annotationParser = new ClassParser($this);
        }

        return $this->_annotationParser->getClassAnnotation($name);
    }
}
