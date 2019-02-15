<?php

namespace Clew;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass Clew\Token
 */
class TokenTest extends TestCase
{
    /**
     * @param string $value
     * @param bool $isRaw
     * @param string $expected
     * @covers ::__construct
     * @covers ::format
     * @dataProvider provideTestFormat
     */
    public function testFormat(string $value, bool $isRaw, string $expected): void
    {
        $f   = DefaultArgumentFormat::getInstance();
        $obj = new Token($value, $isRaw);
        $this->assertSame($expected, $obj->format($f));
    }

    /**
     * @return array
     */
    public function provideTestFormat(): array
    {
        return [
            ["asdf", true, "asdf"],
            ["asdf", false, "asdf"],
            ["hoge fuga", true, "hoge fuga"],
            ["hoge fuga", false, "\"hoge fuga\""],
        ];
    }

    /**
     * @covers ::__construct
     * @covers ::getValue
     */
    public function testGetValue(): void
    {
        $obj = new Token("hoge fuga", false);
        $this->assertSame("hoge fuga", $obj->getValue());
    }

    /**
     * @covers ::__construct
     * @covers ::isRaw
     */
    public function testIsRaw(): void
    {
        $obj1 = new Token("hoge fuga", true);
        $obj2 = new Token("hoge fuga", false);
        $this->assertTrue($obj1->isRaw());
        $this->assertFalse($obj2->isRaw());
    }
}
