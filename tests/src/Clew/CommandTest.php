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
        $expected = [
            new Token("2", false),
            new Token("-3", false),
            new Token("1", false),
            new Token("", false),
            new Token("", false),
        ];
        $obj1     = new Command([2, -3, true, false, null]);
        $this->assertEquals($expected, $obj1->getArguments());
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
        $expected = [
            new Token("test", false),
            new Token("aaa", false),
            new Token("bbb", false),
            new Token("ccc", false),
        ];
        $obj = new Command(["test", "aaa", "bbb", "ccc"]);
        $this->assertEquals($expected, $obj->getArguments());
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

    /**
     * @covers ::__construct
     * @covers ::pipeTo
     */
    public function testPipeTo(): void
    {
        $expected = [
            new Token("ls", false),
            new Token("-la", false),
            new Token("|", true),
            new Token("grep", false),
            new Token(".txt", false),
        ];

        $c1 = new Command(["ls", "-la"]);
        $c2 = new Command(["grep", ".txt"]);
        $c3 = $c1->pipeTo($c2);
        $this->assertEquals($expected, $c3->getArguments());
    }

    /**
     * @param int $type
     * @param bool $appending
     * @param string $symbol
     * @dataProvider provideTestRedirectTo
     * @covers ::__construct
     * @covers ::redirectTo
     */
    public function testRedirectTo($type, $appending, $symbol): void
    {
        $expected = [
            new Token("somecmd", false),
            new Token("arg1", false),
            new Token("arg2", false),
            new Token($symbol, true),
            new Token("/path/to/file", false),
        ];

        $c1 = new Command(["somecmd", "arg1", "arg2"]);
        $c2 = $c1->redirectTo("/path/to/file", $type, $appending);
        $this->assertEquals($expected, $c2->getArguments());
    }

    /**
     * @return array
     */
    public function provideTestRedirectTo(): array
    {
        return [
            [Command::REDIRECT_STDIN, false, "<"],
            [Command::REDIRECT_STDIN, true, "<"],
            [Command::REDIRECT_STDOUT, false, ">"],
            [Command::REDIRECT_STDOUT, true, ">>"],
            [Command::REDIRECT_STDERR, false, "2>"],
            [Command::REDIRECT_STDERR, true, "2>>"],
        ];
    }

    /**
     * @covers ::__construct
     * @covers ::redirectTo
     */
    public function testRedirectToByDefault(): void
    {
        $expected = [
            new Token("somecmd", false),
            new Token(">", true),
            new Token("/path/to/file", false),
        ];

        $c1 = new Command(["somecmd"]);
        $c2 = $c1->redirectTo("/path/to/file");
        $this->assertEquals($expected, $c2->getArguments());
    }

    /**
     * @covers ::__construct
     * @covers ::redirectTo
     */
    public function testRedirectToFailByInvalidType(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $c1 = new Command(["somecmd"]);
        $c1->redirectTo("/path/to/file", 123);
    }

    /**
     * @param Command $c
     * @param bool $expected
     * @covers ::__construct
     * @covers ::redirectTo
     * @covers ::hasStdErr
     * @dataProvider provideTestHasStdErr
     */
    public function testHasStdErr(Command $c, $expected): void
    {
        $this->assertSame($expected, $c->hasStdErr());
    }

    /**
     * @return array
     */
    public function provideTestHasStdErr(): array
    {
        $c1 = new Command(["somecmd"]);
        $c2 = $c1->redirectTo("/path/to/out.log", Command::REDIRECT_STDOUT);
        $c3 = $c1->redirectTo("/path/to/err.log", Command::REDIRECT_STDERR);
        $c4 = $c2->redirectTo("/path/to/err.log", Command::REDIRECT_STDERR);
        $c5 = $c3->redirectTo("/path/to/out.log", Command::REDIRECT_STDOUT);
        $c6 = $c1->redirectTo("/path/to/in.txt", Command::REDIRECT_STDIN);
        return [
            [$c1, false],
            [$c2, false],
            [$c3, true],
            [$c4, true],
            [$c5, true],
            [$c6, false],
        ];
    }
}
