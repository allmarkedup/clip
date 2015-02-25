<?php namespace Amu\Clip;

use Amu\Clip\Input;
use Amu\Clip\Output;
use Amu\Clip\Annotations\ClassParser;

abstract class Command
{
    protected $console;

    protected $classAnnotationParser;

    abstract public function execute(Input $input, Output $output); 

    public function getConsole()
    {
        return $this->console;
    }

    public function setConsole(Console $console)
    {
        $this->console = $console;
    }

    public function getName()
    {
        return $this->getClassAnnotation('command') ?: strtolower($this->getClassName());
    }

    public function getDescription()
    {
        return $this->getClassAnnotation('description') ?: '';
    }

    private function getClassName()
    {
        return implode('', array_slice(explode('\\', get_class($this)), -1));
    }

    private function getClassAnnotation($name)
    {
        if (! $this->classAnnotationParser) {
            $this->classAnnotationParser = new ClassParser($this);
        }   
        return $this->classAnnotationParser->getAnnotation($name);
    }
}
