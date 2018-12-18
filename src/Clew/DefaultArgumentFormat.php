<?php

namespace Clew;

/**
 * 各種 Linux ディストリビューションを含む、一般的な ArgumentFormat の実装です。
 */
class DefaultArgumentFormat implements ArgumentFormat
{
    /**
     * @codeCoverageIgnore
     */
    private function __construct()
    {
    }

    /**
     * 唯一の DefaultArgumentFormat インスタンスを返します。
     *
     * @return DefaultArgumentFormat
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
        return preg_match("/\\s/s", $str) ? '"' . $this->escapeQuote($str) . '"' : $this->escape($str);
    }

    /**
     * ファイルシステムのパスについてエスケープ処理を行います。
     * この実装は format() と同じ結果を返します。
     *
     * @param string $str 処理対象のパス
     * @return string 処理結果
     */
    public function formatFilePath($str)
    {
        return $this->format($str);
    }

    /**
     * @param string $str
     * @return string
     */
    private function escapeQuote($str)
    {
        return str_replace("\"", "\\\"", $str);
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
            $specials     = ["\\", "\"", "'", "`", "*", "?", "[", "]", "(", ")", "<", ">", "$", ";", "&", "|", "~", "#", "%", "="];
            $replacements = [];
            foreach ($specials as $chr) {
                $replacements[$chr] = "\\{$chr}";
            }
        }
        // @codeCoverageIgnoreEnd

        return strtr($str, $replacements);
    }
}
