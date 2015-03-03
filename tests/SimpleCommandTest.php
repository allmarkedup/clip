<?php namespace Amu\Clip\Tests;

Use Amu\Clip\Input;
Use Amu\Clip\Output;
Use Amu\Clip\Commands\Command;

class SimpleUnannotatedCommand extends Command
{
    public function execute(Input $input, Output $input) {}
}

/**
 * @command simple-test
 * @description This is a test description
 **/
class SimpleCommand extends Command
{
    public function execute(Input $input, Output $input) {}
}

class SimpleCommandTest extends \PHPUnit_Framework_TestCase
{
    public function testGetCommandNameGeneratedFromClassName()
    {
        $command = new SimpleUnannotatedCommand();
        $this->assertEquals('simple-unannotated', $command->getName());
    }

    public function testGetCommandNameFromAnnotation()
    {
        $command = new SimpleCommand();
        $this->assertEquals('simple-test', $command->getName());
    }

    public function testGetDescriptionFromAnnotation()
    {
        $command = new SimpleCommand();
        $this->assertEquals('This is a test description', $command->getDescription());
    }
}