<?php

namespace Clew;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass Clew\Command
 */
class CommandTest extends TestCase
{
    /**
     * 第 1 引数にはコマンド名が指定されている必要があります。
     *
     * @covers ::__construct
     * @covers ::checkArguments
     */
    public function testConstructFailByEmptyArguments(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Command([]);
    }

    /**
     * 第 2 引数が空の場合でも例外が発生しないことを確認します。
     *
     * @covers ::__construct
     * @covers ::checkArguments
     */
    public function testConstructSuucess(): void
    {
        $this->expectNotToPerformAssertions();
        new Command(["test"]);
    }

    /**
     * 数値, true, false, null などの値が文字列に変換されることを確認します。
     *
     * @covers ::__construct
     * @covers ::cleanArguments
     */
    public function testConstructByScalar(): void
    {
        $obj1     = new Command([2, -3, true, false, null]);
        $expected = ["2", "-3", "1", "", ""];
        $this->assertSame($expected, $obj1->getArguments());
    }

    /**
     * 第 2 引数の配列内に 0 以上 255 以下の整数以外の値を指定された場合、
     * 例外が発生することを確認します。
     *
     * @param mixed $num
     * @dataProvider provideTestConstructByInvalidExitStatus
     * @covers ::__construct
     * @covers ::<private>
     */
    public function testConstructByInvalidExitStatus($num): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Command(["test"], [0, 1, $num, 2]);
    }

    /**
     * @return array
     */
    public function provideTestConstructByInvalidExitStatus(): array
    {
        return [
            ["hoge"],
            [256],
            [-1],
            [2.5],
        ];
    }

    /**
     * 第 2 引数の配列の値がすべて 0 以上 255 以下の整数の場合に初期化に成功することを確認します。
     *
     * @param mixed $num
     * @dataProvider provideTestConstructByValidExitStatus
     * @covers ::__construct
     * @covers ::<private>
     */
    public function testConstructByValidExitStatus($num): void
    {
        $this->expectNotToPerformAssertions();
        new Command(["test"], [3, 4, 5, $num]);
    }

    /**
     * @return array
     */
    public function provideTestConstructByValidExitStatus(): array
    {
        return [
            [0],
            [255],
            ["1"],
        ];
    }

    /**
     * 第 2 引数に整数を指定した場合、要素数 1 の配列を指定した場合と同じ結果になることを確認します。
     *
     * @covers ::__construct
     * @covers ::cleanExpectedExits
     */
    public function testConstructByIntExitStatus(): void
    {
        $cmd1 = new Command(["test"], [1]);
        $cmd2 = new Command(["test"], 1);
        $this->assertEquals($cmd1, $cmd2);
    }

    /**
     * @covers ::getArguments
     */
    public function testGetArguments(): void
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
    public function testValidateExitCode(array $expectedExits, $code, $expected): void
    {
        $obj = new Command(["test"], $expectedExits);
        $this->assertSame($expected, $obj->validateExitCode($code));
    }

    /**
     * @return array
     */
    public function provideTestValidateExitCode(): array
    {
        return [
            [[], 127, true],
            [[0, 1, 2], 0, true],
            [[0, 1, 2], 127, false],
        ];
    }
}
