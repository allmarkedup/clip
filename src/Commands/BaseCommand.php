<?php namespace Amu\Clip\Commands;

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
}
