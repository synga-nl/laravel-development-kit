<?php
/**
 * Created by PhpStorm.
 * User: roypouls
 * Date: 25/04/2018
 * Time: 21:04
 */

namespace Synga\LaravelDevelopment\Console\Command\Modified;


use Illuminate\Console\GeneratorCommand;
use Synga\LaravelDevelopment\RunCommandTrait;

class ViewMakeCommand extends GeneratorCommand
{
    use RunCommandTrait, ModifyCommandTrait;

    protected function getStub()
    {
        // TODO: Implement getStub() method.
    }
}