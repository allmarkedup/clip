<?php namespace Amu\Clip;

use League\CLImate\CLImate as Writer;

class Output
{
    private $stdWriter;

    public function __construct()
    {
        $this->stdWriter = $this->getWriter();
    }

    public function __call($name, $args)
    {
        if (in_array($name, ['input', 'table', 'padding', 'columns', 'border', 'columns', 'progress', 'json', 'dump', 'flank', 'animation'])) {
            return call_user_func_array(array($this->getWriter(), $name), $args);
        }

        return call_user_func_array(array($this->stdWriter, $name), $args);
    }

    protected function getWriter()
    {
        return new Writer();
    }
}
