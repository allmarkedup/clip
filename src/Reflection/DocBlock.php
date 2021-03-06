<?php namespace Amu\Clip\Reflection;

class DocBlock
{
    private $keyPattern = "[A-z0-9\_\-]+";

    private $endPattern = "[ ]*(?:@|\r\n|\n)";

    protected $docBlock;

    public function __construct($docBlock)
    {
        $this->docBlock = $docBlock;
    }

    public function getAnnotations()
    {
        return $this->parseAnnotations($this->docBlock);
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
        if ($value === '') {
            return;
        }

        return $value;
    }
}
