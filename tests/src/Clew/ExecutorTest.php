<?php

namespace Clew;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass Clew\Executor
 */
class ExecutorTest extends TestCase
{
    /**
     * @var string
     */
    private $testDir;

    protected function setUp(): void
    {
        $this->testDir = TEST_DATA_DIR . "/Clew/Executor";
    }

    /**
     * executeArray() が execute() と同じ結果を返すことを確認します。
     *
     * @covers ::getInstance
     * @covers ::executeArray
     */
    public function testExecuteArray(): void
    {
        $obj  = Executor::getInstance();
        $res1 = $obj->executeArray([PHP_BINARY, "{$this->testDir}/test.inc"], 2);
        $res2 = $obj->execute(new Command([PHP_BINARY, "{$this->testDir}/test.inc"], 2));
        $this->assertEquals($res1, $res2);
        $this->assertSame("Hello, World!" . PHP_EOL . "test output", $res1->getOutput());
        $this->assertSame("Hello, World!" . PHP_EOL . "test error", $res1->getError());
    }
}
