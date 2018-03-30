<?php

namespace Clew;

use InvalidArgumentException;

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
        $this->checkArguments($arguments);
        $this->arguments     = $arguments;
        $this->expectedExits = $this->cleanExpectedExits($expectedExits);
    }

    /**
     * @param array $arguments
     * @throws InvalidArgumentException
     */
    private function checkArguments(array $arguments)
    {
        if (!count($arguments)) {
            throw new InvalidArgumentException("Command name is required.");
        }
    }

    /**
     * @param array $expectedExits
     * @return int[]
     * @throws InvalidArgumentException
     */
    private function cleanExpectedExits(array $expectedExits)
    {
        $result = [];
        foreach ($expectedExits as $num) {
            if (!$this->validateNumberFormat($num)) {
                throw new InvalidArgumentException("Invalid exit code: {$num}");
            }
            $result[] = (int) $num;
        }
        return $result;
    }

    /**
     * @param int $num
     * @return bool
     */
    private function validateNumberFormat($num)
    {
        if (is_int($num)) {
            return (0 <= $num && $num < 256);
        }

        $sNum = (string) $num;
        $iNum = (int) $num;
        return preg_match("/\\A[0-9]+\\z/", $sNum) && $this->validateNumberFormat($iNum);
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
