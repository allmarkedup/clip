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
        $result = $this->parse($this->argv, $signature);
        $this->validateInput($result, $signature);
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

    protected function validateInput($input, $signature)
    {
                    echo '<pre>';
                    print_r($input);
                    echo '</pre>';
    }

    protected function parse($argv, $signature)
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
        
        $getopt->parse($argv);
        $opts = $getopt->getOptions();
        $operands = $getopt->getOperands();

        $args = [];
        for ($i = 0; $i < count($signature['args']); $i++) {
            if (isset($operands[$i])) {
               $args[$signature['args'][$i]['name']] = $operands[$i];
            } else {
                break;
            }
        }

        return compact('args', 'opts');
    }

}
