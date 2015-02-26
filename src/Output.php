<?php namespace Amu\Clip;

use League\CLImate\CLImate as Writer;

class Output
{
    private $stdWriter;

    public function __construct()
    {
        $this->stdWriter = $this->getWriter();
    }

    public function input($text)
    {
        return $this->getWriter()->input($text);
    }

    public function table($data)
    {
        return $this->getWriter()->table($data);
    }

    public function padding($width)
    {
        return $this->getWriter()->padding($width);
    }

    public function __call($name, $args)
    {
        return call_user_func_array(array($this->stdWriter, $name), $args);
    }

    protected function getWriter()
    {
        return new Writer();
    }
}
