<?php

namespace Clew;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass Clew\CommandResult
 */
class CommandResultTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::getOutput
     */
    public function testGetOutput(): void
    {
        $obj = new CommandResult("test");
        $this->assertSame("test", $obj->getOutput());
    }

    /**
     * @covers ::__construct
     * @covers ::getError
     */
    public function testGetError(): void
    {
        $obj1 = new CommandResult("test");
        $this->assertSame("", $obj1->getError());

        $obj2 = new CommandResult("test", "hogehoge");
        $this->assertSame("hogehoge", $obj2->getError());
    }

    /**
     * @covers ::__construct
     * @covers ::getExitStatus
     */
    public function testGetExitStatus(): void
    {
        $obj1 = new CommandResult("test");
        $this->assertSame(0, $obj1->getExitStatus());

        $obj2 = new CommandResult("test", "", 127);
        $this->assertSame(127, $obj2->getExitStatus());
    }
}
