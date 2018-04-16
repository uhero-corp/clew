<?php

namespace Clew;

/**
 * コマンドの実行結果をあらわすクラスです。
 */
class CommandResult
{
    /**
     * 標準出力の内容です。
     * 存在しない場合は空文字列となります。
     *
     * @var string
     */
    private $output;

    /**
     * 標準エラー出力の内容です。
     * 存在しない場合は空文字列となります。
     *
     * @var string
     */
    private $error;

    /**
     * 終了ステータスをあらわす整数です。
     *
     * @var int
     */
    private $exitStatus;

    /**
     * @param string $output
     * @param string $error
     * @param int $exitStatus
     */
    public function __construct($output, $error = "", $exitStatus = 0)
    {
        $this->output     = $output;
        $this->error      = $error;
        $this->exitStatus = (int) $exitStatus;
    }

    /**
     * @return string
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * @return string
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @return int
     */
    public function getExitStatus()
    {
        return $this->exitStatus;
    }
}
