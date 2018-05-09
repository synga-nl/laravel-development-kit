<?php
namespace Synga\LaravelDevelopment\Tests;

use Synga\InteractiveConsoleTester\Test\BaseFlowTest;
use Synga\LaravelDevelopment\Tests\Input\MenuInputTest;

class NewPackageCommendFlowTest extends BaseFlowTest
{
    public function getTests()
    {
        return [
            'New package',
            new MenuInputTest('joehoe/joehoe'),
            'exit'
        ];
    }
}