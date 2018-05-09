<?php

namespace Synga\LaravelDevelopment\Tests\Input;

use Synga\InteractiveConsoleTester\Test\FlowTest;
use Synga\InteractiveConsoleTester\Test\InputTest;
use Synga\LaravelDevelopment\Tests\NewPackageCommendFlowTest;

class PackageMenuInputTest extends InputTest
{
    public function testBefore(FlowTest $flowTest, array $buffer, array $localBuffer)
    {
        $lineNumber = $this->findMenuKey($this->getInput(), $localBuffer);

        if(!isset($lineNumber)){
            NewPackageCommendFlowTest::run($flowTest->getProcess()->getCommand());
        }
    }

    public function testAfter(FlowTest $flowTest, array $buffer, array $localBuffer)
    {
        $this->findMenuKey($this->getInput(), $localBuffer);
    }

    public function cleanUp(FlowTest $flowTest, array $buffer, array $localBuffer)
    {
//        exec('rm -rf ');
    }
}