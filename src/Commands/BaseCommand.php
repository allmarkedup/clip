<?php namespace Amu\Clip\Commands;

use Amu\Clip\Input;
use Amu\Clip\Output;

abstract class BaseCommand extends Command
{
    /**
     * @type option
     *
     * @long help
     * @short h
     * @description Show help for this command
     */
    protected $help = false;

    /**
     * @type option
     *
     * @long version
     * @short v
     * @description Show version information
     */
    protected $version = false;

    protected function beforeExecute(Input &$input, Output &$output)
    {
        if (! empty($this->help)) {
            $this->help($input, $output);
            return false;
        }
        if (! empty($this->version)) {
            $this->version($input, $output);
            return false;
        }
    }

    protected function help(Input $input, Output $output)
    {
        if ($desc = $this->getDescription()) {
            $output->br()->yellow($desc);
        }

        $sig = $this->getSignature();
        $args = implode(' ', array_map(function ($arg) {
            $name = '<'.$arg['name'].'>';

            return $arg['required'] ? $name : '['.$name.']';
        }, $sig['args']));

        $output->br()->out('Usage: php '.$_SERVER['PHP_SELF'].' '.$this->getName().' '.$args);
        
        if (count($sig['opts'])) {
            $output->br();
            $optStrings = [];
            foreach ($sig['opts'] as $opt) {
                $long = $opt['long'] ? '--'.$opt['long'] : null;
                $short = $opt['short'] ? '-'.$opt['short'] : null;
                $optStrings[] = [
                    'label' => '  '.implode(', ', array_filter([$long, $short], 'strlen')).' ',
                    'description' => $opt['description'],
                ];
            }
            $maxOptsLen = 0;
            foreach ($optStrings as $os) {
                if (mb_strlen($os['label']) > $maxOptsLen) {
                    $maxOptsLen = mb_strlen($os['label']);
                }
            }
            $pad = $output->padding($maxOptsLen + 5, ' ');
            foreach ($optStrings as $os) {
                $pad->label($os['label'])->result($os['description']);
            }
        }

        $output->br();
    }

    protected function version(Input $input, Output $output)
    {
        $console = $this->getConsole();
        $output->out($console->getName() . ($console->getVersion() ? ' version ' . $console->getVersion() : '') );
    }
}
