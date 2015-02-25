<?php namespace Amu\Clip\Annotations;

class ClassParser extends AnnotationParser
{
    public function __construct($class)
    {
        if (is_object($class)) {
            $this->reflectionObject = new \ReflectionObject($class);
        } else {
            $this->reflectionObject = new \ReflectionClass($class);
        }
    }
}
