<?php namespace Amu\Clip\Reflection;

use Amu\Clip\Exception\Exception;
use Amu\Clip\Exception\AnnotationNotFoundException as NotFoundException;

class CommandProperty extends Reflected
{
    public function __construct($arg, $name = null)
    {
        if ($arg instanceof \ReflectionProperty) {
            $this->reflectionObject = $arg;
        } elseif ($arg instanceof self) {
            $this->reflectionObject = $arg->getReflectionObject();
            $this->docBlock = $arg->getDocBlock();
        } else {
            if (is_null($name)) {
                throw new Exception('You must specify a property name.');
            }
            $this->reflectionObject = new \ReflectionProperty($arg, $name);
        }
    }

    public function getPropertyName()
    {
        return $this->reflectionObject->getName();
    }

    public function getValidationRules()
    {
        try {
            return $this->parseValidationString($this->getAnnotation('validate'));
        } catch (NotFoundException $e) {
            return [];
        }
    }

    protected function parseValidationString($string)
    {
        $rules = [];
        foreach (explode('|', $string) as $rule) {
            if (strpos($rule, ':') !== false) {
                list($key, $value) = explode(':', $rule);
                $rules[$key] = $value;
            } else {
                $rules[$rule] = true;
            }
        }

        return $rules;
    }
}
