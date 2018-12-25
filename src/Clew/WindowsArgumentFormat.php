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
        if ($str === "") {
            return '""';
        }
        $escaped = $this->escape($str);
        return preg_match("/\\s/s", $str) ? '^"' . $escaped . '^"' : $escaped;
    }

    /**
     * 指定された文字列について MS-DOS 上で妥当なファイル名として扱われるよう書式化します。
     * 下記の要領で変換を行います。
     *
     * - '/' (0x2f) を '\' (0x5c) に置換
     * - 文字列の先頭と末尾に '"' を付与
     *
     * @param string $str 処理対象のパス
     * @return string 処理結果
     */
    public function formatFilePath($str)
    {
        return '"' . str_replace("/", "\\", $str) . '"';
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
