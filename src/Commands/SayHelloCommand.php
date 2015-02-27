<?php namespace Amu\Clip\Commands;

use Amu\Clip\Input;
use Amu\Clip\Output;
use Amu\Clip\Commands\BaseCommand;

/**
 * @command say:hello
 * @description Say hello to someone
 */
class SayHelloCommand extends BaseCommand
{
    /**
     * @type argument
     * 
     * @description Who to say hello to
     * @validate required
     */
    protected $name;

    /**
     * @type argument
     * 
     * @description Who to say hello to
     * @value required
     */
    protected $lastname;

    /**
     * @type option
     * 
     * @description Use chatty mode
     */
    protected $chatty = false;

    public function execute(Input $input, Output $output)
    {
        echo $this->name;
    }
}
