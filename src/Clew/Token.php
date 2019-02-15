<?php

namespace Clew;

/**
 * コマンドを構成する各種文字列です。
 * コマンド, コマンドライン引数, パイプやリダイレクト等の各種記号などが該当します。
 */
class Token
{
    /**
     * @var string
     */
    private $value;

    /**
     * @var bool
     */
    private $isRaw;

    /**
     * @param string $value
     * @param bool $isRaw
     */
    public function __construct($value, $isRaw)
    {
        $this->value = $value;
        $this->isRaw = $isRaw;
    }

    /**
     * @param ArgumentFormat $format
     * @return string
     */
    public function format(ArgumentFormat $format)
    {
        return $this->isRaw ? $this->value : $format->format($this->value);
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return bool
     */
    public function isRaw()
    {
        return $this->isRaw;
    }
}
