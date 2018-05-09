<?php
namespace Synga\LaravelDevelopment\Tests;

use Synga\InteractiveConsoleTester\Test\BaseFlowTest;
use Synga\LaravelDevelopment\Tests\Input\PackageMenuInputTest;

class ConsoleMakeCommandFlowTest extends BaseFlowTest
{
    public function getTests()
    {
        return [
            new PackageMenuInputTest('joehoe/joehoe'),
            'exit'
        ];
    }
}