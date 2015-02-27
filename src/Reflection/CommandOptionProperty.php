<?php namespace Amu\Clip\Reflection;

use Amu\Clip\Exception\AnnotationNotFoundException as NotFoundException;

class CommandOptionProperty extends CommandProperty
{
    public function getLongName()
    {
        try {
            return $this->getAnnotation('long') === true ? $this->getName() : $this->getAnnotation('long');
        } catch (NotFoundException $e){
            if ( is_null($this->getShortName()) ) {
                return $this->getName();
            }
            return null;
        }
    }

    public function getShortName()
    {
        try {
            return $this->getAnnotation('short') === true ? $this->getName()[0] : $this->getAnnotation('short');
        } catch (NotFoundException $e){
            return null;
        }
    }

    public function getInputValueType()
    {
        try {
            $valueType = $this->getAnnotation('value');
            switch ($valueType) {
                case 'optional':
                case 'required':
                    $val = $valueType;
                    break;
                case 'none':
                default:
                    $val = null;
            }
            return $val;
        } catch (NotFoundException $e){
            return null;
        }
    }
}