<?php

namespace Synga\LaravelDevelopment\Tests\Input;

use Synga\InteractiveConsoleTester\Test\FlowTest;
use Synga\InteractiveConsoleTester\Test\InputTest;

class MenuInputTest extends InputTest
{
    public function testBefore(FlowTest $flowTest, array $buffer, array $localBuffer)
    {
        $this->setInput(implode(' ', explode('/', $this->getInput())));
    }

    public function testAfter(FlowTest $flowTest, array $buffer, array $localBuffer)
    {
        // TODO: Implement testAfter() method.
    }

    public function cleanUp(FlowTest $flowTest, array $buffer, array $localBuffer)
    {
//        echo 'pony';
        var_dump('php artisan packager:remove ' . $this->getInput());
        FlowTest::run('php artisan packager:remove ' . $this->getInput(), ['y']);
//        echo 'deleted';
    }
}