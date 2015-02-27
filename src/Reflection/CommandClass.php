<?php namespace Amu\Clip\Reflection;

use Amu\Clip\Exception\AnnotationNotFoundException as NotFoundException;

class CommandClass extends Reflected
{
    public function __construct($arg)
    {
        if ( $arg instanceof \ReflectionObject || $arg instanceof \ReflectionClass ) {
            $this->reflectionObject = $arg;
        } elseif (is_object($arg)) {
            $this->reflectionObject = new \ReflectionObject($arg);
        } else {
            $this->reflectionObject = new \ReflectionClass($arg);
        }
    }

    public function getName()
    {
        try {
            return $this->getAnnotation('command');
        } catch (NotFoundException $e){
            return $this->getNameFromClassName();
        }
    }

    public function getAnnotatedProperties()
    {
        return array_filter($this->getReflectedProperties(), function($property){
            return $property->hasAnnotations();
        });
    }

    public function getReflectedProperties()
    {
        return array_map(function($property){
            $prop = new CommandProperty($property);
            try {
                if ($prop->getAnnotation('type') === 'argument') {
                    return new CommandArgumentProperty($prop);
                }
                if ($prop->getAnnotation('type') === 'option') {
                    return new CommandOptionProperty($prop);
                }
            } catch (NotFoundException $e) {
                // do nothing
            }
            return $prop;
        }, $this->reflectionObject->getProperties());
    }

    public function getOptionProperties()
    {
        return array_filter($this->getAnnotatedProperties(), function($property) {
            return ($property instanceof CommandOptionProperty);
        });
    }

    public function getArgumentProperties()
    {
        return array_filter($this->getAnnotatedProperties(), function($property) {
            return ($property instanceof CommandArgumentProperty);
        });
    }

    // protected function filterProperties($key, $value)
    // {
    //     return array_filter($this->getAnnotatedProperties(), function($property) use ($key, $value) {
    //         try {
    //             return $property->getAnnotation($key) === $value;
    //         } catch (AnnotationNotFoundException $e) {
    //             return false;
    //         }
    //     });
    // }

    protected function getNameFromClassName()
    {
        $className = implode('', array_slice(explode('\\', $this->name), -1));
        $className = str_replace('Command', '', $className);
        preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $className, $matches);
        $ret = $matches[0];
        foreach ($ret as &$match) {
            $match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
        }

        return implode('-', $ret);
    }
}
