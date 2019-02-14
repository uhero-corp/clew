<?php

namespace Clew;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass Clew\DefaultArgumentFormat
 */
class DefaultArgumentFormatTest extends TestCase
{
    /**
     * @covers ::getInstance
     */
    public function testGetInstance(): void
    {
        $obj1 = DefaultArgumentFormat::getInstance();
        $obj2 = DefaultArgumentFormat::getInstance();

        $this->assertInstanceOf(DefaultArgumentFormat::class, $obj1);
        $this->assertSame($obj1, $obj2);
    }

    /**
     * @param string $str
     * @param string $expected
     * @covers ::format
     * @covers ::<private>
     * @dataProvider provideTestFormat
     */
    public function testFormat($str, $expected): void
    {
        $obj = DefaultArgumentFormat::getInstance();
        $this->assertSame($expected, $obj->format($str));
    }

    /**
     * @return array
     */
    public function provideTestFormat(): array
    {
        return [
            ["", "\"\""],
            ["test", "test"],
            ["John Smith", "\"John Smith\""],
            ["\"John Smith\"", "\"\\\"John Smith\\\"\""],
            ["<john-smith@example.com>", "\\<john-smith@example.com\\>"],
            ["John Smith <john-smith@example.com>", "\"John Smith <john-smith@example.com>\""],
            ["\"John Smith\" <john-smith@example.com>", "\"\\\"John Smith\\\" <john-smith@example.com>\""],
        ];
    }

    /**
     * format() と同じ結果を返すことを確認します。
     *
     * @param string $str
     * @param string $expected
     * @covers ::formatFilePath
     * @covers ::<private>
     * @covers ::escape
     * @dataProvider provideTestFormat
     */
    public function testFormatFilePath($str, $expected): void
    {
        $obj = DefaultArgumentFormat::getInstance();
        $this->assertSame($expected, $obj->formatFilePath($str));
    }

}
