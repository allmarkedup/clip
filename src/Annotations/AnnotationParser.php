<?php namespace Amu\Clip\Annotations;

abstract class AnnotationParser
{
    protected $reflectionObject;

    protected $annotations;

    private $keyPattern = "[A-z0-9\_\-]+";

    private $endPattern = "[ ]*(?:@|\r\n|\n)";

    public function getAnnotation($annotation)
    {
        $annotations = $this->getAnnotations();
        
        return isset($annotations[$annotation]) ? $annotations[$annotation] : null;
    }

    public function getAnnotations()
    {
        if (! $this->annotations) {
            $this->annotations = $this->parseAnnotations($this->reflectionObject->getDocComment());
        }

        return $this->annotations;
    }

    protected function parseAnnotations($docBlock)
    {
        $pattern = "/@(?=(.*)".$this->endPattern.")/U";
        preg_match_all($pattern, $docBlock, $matches);
        $annots = [];
        foreach ($matches[1] as $rawParameter) {
            if (preg_match("/^(".$this->keyPattern.") (.*)$/", $rawParameter, $match)) {
                if (isset($annots[$match[1]])) {
                    $annots[$match[1]] = array_merge((array) $annots[$match[1]], (array) $match[2]);
                } else {
                    $annots[$match[1]] = $this->parseValue($match[2]);
                }
            } elseif (preg_match("/^".$this->keyPattern."$/", $rawParameter, $match)) {
                $annots[$rawParameter] = true;
            } else {
                $annots[$rawParameter] = null;
            }
        }

        return $annots;
    }

    protected function parseValue($value = null)
    {
        if ($value === 'null') {
            return;
        }
        if (($json = json_decode($value, true)) !== null) {
            return $json;
        }

        return $value;
    }
}
