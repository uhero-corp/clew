<?php

namespace Clew;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass Clew\DefaultArgumentFormat
 */
class DefaultArgumentFormatTest extends TestCase
{
    /**
     * @param string $str
     * @param string $expected
     * @covers ::format
     * @covers ::<private>
     * @dataProvider provideTestFormat
     */
    public function testFormat($str, $expected)
    {
        $obj = new DefaultArgumentFormat();
        $this->assertSame($expected, $obj->format($str));
    }

    /**
     * @return array
     */
    public function provideTestFormat()
    {
        return [
            ["test", "test"],
            ["John Smith", "\"John Smith\""],
            ["\"John Smith\"", "\"\\\"John Smith\\\"\""],
            ["<john-smith@example.com>", "\\<john-smith@example.com\\>"],
            ["John Smith <john-smith@example.com>", "\"John Smith <john-smith@example.com>\""],
            ["\"John Smith\" <john-smith@example.com>", "\"\\\"John Smith\\\" <john-smith@example.com>\""],
        ];
    }
}
