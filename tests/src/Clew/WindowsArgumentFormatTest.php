<?php

namespace Clew;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass Clew\WindowsArgumentFormat
 */
class WindowsArgumentFormatTest extends TestCase
{
    /**
     * @covers ::getInstance
     */
    public function testGetInstance()
    {
        $obj1 = WindowsArgumentFormat::getInstance();
        $obj2 = WindowsArgumentFormat::getInstance();

        $this->assertInstanceOf(WindowsArgumentFormat::class, $obj1);
        $this->assertSame($obj1, $obj2);
    }

    /**
     * @param string $str
     * @covers ::format
     * @covers ::<private>
     * @dataProvider provideTestFormat
     */
    public function testFormat($str, $expected)
    {
        $obj = WindowsArgumentFormat::getInstance();
        $this->assertSame($expected, $obj->format($str));
    }

    /**
     * @return array
     */
    public function provideTestFormat()
    {
        return [
            ["hogehoge", "hogehoge"],
            ["hoge fuga", "^\"hoge fuga^\""],
            ["abc%PATH%xyz", "abc^%PATH^%xyz"],
            ["I say \"Hello World\"", "^\"I say \\^\"Hello World\\^\"^\""],
        ];
    }
}
