<?php namespace Amu\Clip\Annotations;

class ClassParser extends AnnotationParser
{
    protected $classAnnotations;

    protected $annotatedProperties;

    public function __construct($class)
    {
        if (is_object($class)) {
            $this->reflectionObject = new \ReflectionObject($class);
        } else {
            $this->reflectionObject = new \ReflectionClass($class);
        }
    }

    public function getClassAnnotation($annotation)
    {
        $annotations = $this->getClassAnnotations();

        return isset($annotations[$annotation]) ? $annotations[$annotation] : null;
    }

    public function getClassAnnotations()
    {
        if (! $this->classAnnotations) {
            $this->classAnnotations = $this->parseAnnotations($this->reflectionObject->getDocComment());
        }

        return $this->classAnnotations;
    }

    // public function 

    public function getAnnotatedProperties()
    {
        if (! $this->annotatedProperties) {
            $annotatedProperties = [];
            $properties = array_filter($this->reflectionObject->getProperties(), function($property){
                return ! empty($property->getDocComment());
            });
            foreach ($properties as $property) {
                $prop = [
                    'property' => $property,
                    'annotations' => $this->parseAnnotations($property->getDocComment()),
                ];
                $this->annotatedProperties[$property->name] = $prop;
            }
        }
        return $this->annotatedProperties;
    }

}
