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
        foreach($signature['opts'] as $opt) {
            $val = Getopt::NO_ARGUMENT;
            if ( $opt['valueType'] === 'optional' ) {
                $val = Getopt::OPTIONAL_ARGUMENT;
            } elseif ( $opt['valueType'] === 'required' ) {
                $val = Getopt::REQUIRED_ARGUMENT;
            }
            $options[] = new Option($opt['short'], $opt['long'], $val);
        }
        $getopt = new Getopt($options);
        
        $getopt->parse($this->argv);
        
        $opts = $getopt->getOptions();
        $args = $getopt->getOperands();

                echo '<pre>';
                print_r($opts);
                echo '</pre>';

                echo '<pre>';
                print_r($args);
                echo '</pre>';
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
