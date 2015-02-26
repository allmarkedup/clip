<?php namespace Amu\Clip\Commands;

abstract class BaseCommand extends Command
{
    /**
     * @type option
     * 
     * @long verbose
     * @short v
     * @description Whether to use verbose mode for output
     */
    protected $verbose = false;

    /**
     * @type option
     * 
     * @long help
     * @short h
     * @description Whether to show help for this command
     */
    protected $help = false;
}