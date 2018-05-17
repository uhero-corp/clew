<?php

namespace Clew;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass Clew\CommandException
 */
class CommandExceptionTest extends TestCase
{
    /**
     * @covers ::setCommandLine
     * @covers ::getCommandLine
     */
    public function testAccessCommandLine()
    {
        $obj = new CommandException("Command failed", 1);
        $obj->setCommandLine("test.sh -n 123");
        $this->assertSame("test.sh -n 123", $obj->getCommandLine());
    }

    /**
     * @covers ::setCommandResult
     * @covers ::getCommandResult
     */
    public function testAccessCommandResult()
    {
        $obj    = new CommandException("Command failed", 1);
        $result = new CommandResult("", "Invalid value", 1);
        $obj->setCommandResult($result);
        $this->assertSame($result, $obj->getCommandResult());
    }
}
