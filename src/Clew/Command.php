<?php

namespace Clew;

class Command
{
    /**
     * コマンド名とその引数の一覧です
     *
     * @var string[]
     */
    private $arguments;

    /**
     * このコマンドによって返される終了ステータスの一覧です
     *
     * @var int
     */
    private $expectedExits;

    /**
     * @param string[] $arguments コマンド名とそれに続く引数の一覧です
     * @param int[] $expectedExits 想定された終了ステータスの一覧。未指定の場合はすべてのステータスコードを許可します
     */
    public function __construct(array $arguments, array $expectedExits = [])
    {
        $this->arguments     = $arguments;
        $this->expectedExits = $expectedExits;
    }

    /**
     * コマンド名およびその引数の一覧を返します。
     *
     * @return string[]
     */
    public function getArguments()
    {
        return $this->arguments;
    }

    /**
     * 指定された終了スタータスが想定されたものかどうかを判定します。
     *
     * @param int $code 終了ステータス
     * @return bool 引数の終了スタータスが想定されたものの場合は true, それ以外は false
     */
    public function validateExitCode($code)
    {
        if (!count($this->expectedExits)) {
            return true;
        }

        return in_array($code, $this->expectedExits);
    }
}
