<?php namespace Amu\Clip\Reflection;

use Amu\Clip\Exception\Exception;
use Amu\Clip\Exception\AnnotationNotFoundException as NotFoundException;

abstract class Reflected
{   
    protected $reflectionObject;

    protected $docBlock;

    public function getDescription()
    {
        try {
            return $this->getAnnotation('description');
        } catch (NotFoundException $e){
            return null;
        }
    }

    public function hasAnnotations()
    {
        return !! count($this->getAnnotations());
    }

    public function getAnnotation($name)
    {
        foreach ($this->getAnnotations() as $annotationName => $value) {
            if ($annotationName == $name) {
                return $value;
            }
        }
        throw new NotFoundException('Annotation ' . $name . ' not found');
    }

    public function getAnnotations()
    {
        return $this->getDocBlock()->getAnnotations();
    }

    public function getReflectionObject()
    {
        return $this->reflectionObject;
    }

    public function getDocBlock()
    {
        if ( ! $this->docBlock ) {
            $this->docBlock = new DocBlock($this->reflectionObject->getDocComment());
        }
        return $this->docBlock;
    }

    public function __call($name, $args)
    {
        if (method_exists($this->reflectionObject, $name)) {
            return call_user_func_array(array($this->reflectionObject, $name), $args);
        }
        throw new Exception('Method not found');
    }
}