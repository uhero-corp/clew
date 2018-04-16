<?php

namespace Clew;

/**
 * 指定された文字列に対して、各システムの CLI の仕様に合わせてエスケープ処理を施すインタフェースです。
 */
interface ArgumentFormat
{
    /**
     * 引数の文字列にエスケープ処理を行い、その結果を返します。
     *
     * @param string $str 処理対象の文字列
     * @return string 処理結果
     */
    public function format($str);

    /**
     * ファイルシステムのパスについてエスケープ処理を行います。
     *
     * @param string $str 処理対象のパス
     * @return string 処理結果
     */
    public function formatFilePath($str);
}
