<?php

namespace Clew;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass Clew\Command
 */
class CommandTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::getArguments
     */
    public function testGetArguments()
    {
        $obj = new Command(["test", "aaa", "bbb", "ccc"]);
        $this->assertSame(["test", "aaa", "bbb", "ccc"], $obj->getArguments());
    }

    /**
     * @param int[] $expectedExits コンストラクタの第 2 引数
     * @param int $code validateExitCode() の引数
     * @param bool $expected 期待される返り値
     * @dataProvider provideTestValidateExitCode
     * @covers ::__construct
     * @covers ::validateExitCode
     */
    public function testValidateExitCode(array $expectedExits, $code, $expected)
    {
        $obj = new Command(["test"], $expectedExits);
        $this->assertSame($expected, $obj->validateExitCode($code));
    }

    /**
     * @return array
     */
    public function provideTestValidateExitCode()
    {
        return [
            [[], 127, true],
            [[0, 1, 2], 0, true],
            [[0, 1, 2], 127, false],
        ];
    }
}
