<?php namespace Amu\Clip;

use Ulrichsg\Getopt\Getopt;
use Ulrichsg\Getopt\Option;

class Input
{
    public function __construct($argv = [])
    {
        $this->argv = $argv;
    }

    public function parseAndValidate($signature)
    {
        $options = [];
        foreach ($signature['opts'] as $opt) {
            $annots = $opt['annotations'];
            $required = isset($annots['validate']['required']) ? Getopt::REQUIRED_ARGUMENT : Getopt::OPTIONAL_ARGUMENT;
            // $short = 
        }
        // $getopt = new Getopt(array(
        //     new Option('a', 'alpha', Getopt::REQUIRED_ARGUMENT),
        //     new Option(null, 'beta', Getopt::OPTIONAL_ARGUMENT),
        //     new Option('c', null)
        // ));

    }

    public function getOption($name)
    {
    }

    public function getOptions()
    {
    }

    public function getArgument($name)
    {
    }

    public function getArguments()
    {
    }
}
