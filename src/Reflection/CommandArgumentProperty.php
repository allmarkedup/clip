<?php namespace Amu\Clip\Reflection;

use Amu\Clip\Exception\AnnotationNotFoundException as NotFoundException;

class CommandArgumentProperty extends CommandProperty
{
    public function getName()
    {
        try {
            return $this->getAnnotation('name') === true ? $this->getName() : $this->getAnnotation('name');
        } catch (NotFoundException $e) {
            return $this->reflectionObject->getName();
        }
    }

    public function isRequired()
    {
        try {
            return $this->getAnnotation('required');
        } catch (NotFoundException $e) {
            $rules = $this->getValidationRules();

            return ! empty($rules['required']);
        }
    }
}
