<?php

namespace Clew;

/**
 * MS-DOS (コマンドプロンプト) 向けの ArgumentFormat の実装です。
 */
class WindowsArgumentFormat implements ArgumentFormat
{
    /**
     * @codeCoverageIgnore
     */
    private function __construct()
    {
    }

    /**
     * 唯一の WindowsArgumentFormat インスタンスを返します。
     *
     * @return WindowsArgumentFormat
     */
    public static function getInstance()
    {
        // @codeCoverageIgnoreStart
        static $instance = null;
        if ($instance === null) {
            $instance = new self();
        }
        // @codeCoverageIgnoreEnd

        return $instance;
    }

    /**
     * @param string $str
     * @return string
     */
    public function format($str)
    {
        $escaped = $this->escape($str);
        return preg_match("/\\s/s", $str) ? '^"' . $escaped . '^"' : $escaped;
    }

    /**
     * @param string $str
     * @return string
     */
    private function escape($str)
    {
        // @codeCoverageIgnoreStart
        static $replacements = null;
        if ($replacements === null) {
            $specials     = ["&", "|", "<", ">", "(", ")", "^", "%"];
            $replacements = ["\r\n" => " ", "\r" => " ", "\n" => " ", "\"" => "\\^\""];
            foreach ($specials as $chr) {
                $replacements[$chr] = "^{$chr}";
            }
        }
        // @codeCoverageIgnoreEnd

        return strtr($str, $replacements);
    }
}
