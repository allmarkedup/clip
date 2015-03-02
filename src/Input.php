<?php namespace Amu\Clip;

use Ulrichsg\Getopt\Getopt;
use Ulrichsg\Getopt\Option;

class Input
{
    protected $argv;

    protected $getOpt;

    protected $errors = [];

    protected $validatedInput = [
        'args' => [],
        'opts' => [],
    ];

    public function __construct($argv = [])
    {
        $this->argv = $argv;
    }

    public function hasErrors()
    {
        return (bool) count($this->errors);
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function parseAndValidate($signature)
    {
        $result = $this->parse($this->argv, $signature);
        $this->validateInput($result, $signature);
    }

    public function getAll($flatten = true)
    {
        return array_merge($this->validatedInput['args'], $this->validatedInput['opts']);
    }

    public function getOption($name)
    {
        return isset($this->validateInput['opts'][$name]) ? $this->validateInput['opts'][$name] : null;
    }

    public function getOptions()
    {
        return isset($this->validateInput['opts']) ? $this->validateInput['opts'] : [];
    }

    public function getArgument($name)
    {
        return isset($this->validateInput['args'][$name]) ? $this->validateInput['args'][$name] : null;
    }

    public function getArguments()
    {
        return isset($this->validateInput['args']) ? $this->validateInput['args'] : [];
    }

    public function getOpts()
    {
        return $this->getOpt;
    }

    protected function validateInput($input, $signature)
    {
        $args = $this->checkArgs($input['args'], $signature['args']);
        $opts = $this->checkOpts($input['opts'], $signature['opts']);
        $this->validatedInput = compact('args', 'opts');

        return $this->validatedInput;
    }

    protected function checkOpts($opts, $signatureOpts)
    {
        $keyedOpts = [];
        foreach ($opts as $key => $value) {
            foreach ($signatureOpts as $sOpt) {
                if (mb_strlen($key) === 1 && $sOpt['short'] === $key) {
                    $keyedOpts[$sOpt['key']] = $value;
                    break;
                }
                if (mb_strlen($key) > 1 && $sOpt['long'] === $key) {
                    $keyedOpts[$sOpt['key']] = $value;
                    break;
                }
            }
        }

        return $keyedOpts;
    }

    protected function checkArgs($args, $signatureArgs)
    {
        $argCount = count($args);
        $sigArgCount = count($signatureArgs);

        // first check that the argument list matches the required argument list
        $requiredArgs = array_filter($signatureArgs, function ($req) {
            return $req['required'];
        });
        $requiredCount = count($requiredArgs);

        if ($requiredCount > $argCount) {
            // not enough arguments
            $missingArgs = array_slice($requiredArgs, ($argCount - $requiredCount));
            $missingArgCount = count($missingArgs);
            $missingArgsNames = array_map(function ($mArg) {
                return $mArg['name'];
            }, $missingArgs);
            $error = 'The '.implode(', ', $missingArgsNames).' argument'.($missingArgCount > 1 ? 's are ' : ' is ').'required.';
            $this->errors[] = new \LengthException($error);
        }

        if ($argCount > $sigArgCount) {
            // too many arguments, don't throw and error
            // but cut the arg list down to the expected length
            $args = array_slice($args, 0, $sigArgCount);
        }

        // TODO: apply specific validation rules

        return $args;
    }

    protected function parse($argv, $signature)
    {
        $options = [];
        foreach ($signature['opts'] as $opt) {
            $val = Getopt::NO_ARGUMENT;
            if ($opt['valueType'] === 'optional') {
                $val = Getopt::OPTIONAL_ARGUMENT;
            } elseif ($opt['valueType'] === 'required') {
                $val = Getopt::REQUIRED_ARGUMENT;
            }
            $options[] = new Option($opt['short'], $opt['long'], $val);
        }
        $getopt = $this->getOpt = new Getopt($options);

        try {
            $getopt->parse($argv);
        } catch (\Exception $e) {
            $this->errors[] = $e;
        }

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
