<?php

namespace Clew;

use DirectoryIterator;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass Clew\DefaultExecutor
 */
class DefaultExecutorTest extends TestCase
{
    /**
     * @var ArgumentFormat
     */
    private $format;

    /**
     * @var string
     */
    private $testDir;

    /**
     * @var string
     */
    private $tmpDir;

    protected function setUp(): void
    {
        $testDir = TEST_DATA_DIR . "/Clew/DefaultExecutor";
        $tmpDir  = "{$testDir}/test01";
        $di      = new DirectoryIterator($tmpDir);
        foreach ($di as $i) {
            $filename = $i->getFilename();
            if (substr($filename, 0, 1) === ".") {
                continue;
            }
            unlink("{$tmpDir}/{$filename}");
        }

        $this->format  = (substr(PHP_OS, 0, 3) === "WIN") ?
                WindowsArgumentFormat::getInstance() : DefaultArgumentFormat::getInstance();
        $this->testDir = $testDir;
        $this->tmpDir  = $tmpDir;
    }

    /**
     * @covers ::__construct
     * @covers ::execute
     * @covers ::<private>
     */
    public function testExecute()
    {
        $obj    = new DefaultExecutor($this->format, $this->tmpDir);
        $cmd    = new Command([PHP_BINARY, "{$this->testDir}/test.inc"], 2);
        $result = $obj->execute($cmd);
        $this->assertSame("Hello, World!" . PHP_EOL . "test output", $result->getOutput());
        $this->assertSame("Hello, World!" . PHP_EOL . "test error", $result->getError());
        $this->assertSame(2, $result->getExitStatus());

        $stderr = file_get_contents($this->tmpDir . "/stderr.log");
        $this->assertSame("Hello, World!" . PHP_EOL . "test error" . PHP_EOL, $stderr);
    }

    /**
     * @covers ::execute
     */
    public function testExecuteFailByUnexpectedExitStatus()
    {
        $this->expectException(CommandException::class);
        $obj = new DefaultExecutor($this->format, $this->tmpDir);
        $cmd = new Command([PHP_BINARY, "{$this->testDir}/test.inc"], [0, 1]);
        $obj->execute($cmd);
    }
}
