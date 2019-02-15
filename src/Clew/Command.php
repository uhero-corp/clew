<?php

namespace Clew;

use InvalidArgumentException;

class Command
{
    /**
     * コマンド名とその引数の一覧です
     *
     * @var Token[]
     */
    private $arguments;

    /**
     * このコマンドによって返される終了ステータスの一覧です
     *
     * @var int
     */
    private $expectedExits;

    /**
     * コマンド名、引数、想定される終了ステータスの一覧を指定して、新しい Command インスタンスを生成します。
     *
     * 第 2 引数の想定される終了ステータスには、配列または 0 以上の整数を指定することができます。
     * 整数を指定した場合、その値の終了ステータスのみを妥当とします。
     * 配列を指定した場合、その配列の各要素の整数を妥当なステータスコードとします。
     * 未指定または空配列を指定した場合、すべてのステータスコードを妥当とします。
     *
     * @param string[] $arguments コマンド名とそれに続く引数の一覧です
     * @param int[] $expectedExits 想定された終了ステータスの一覧 (整数または配列)
     */
    public function __construct(array $arguments, $expectedExits = [])
    {
        $this->arguments     = $this->cleanArguments($arguments);
        $this->expectedExits = $this->cleanExpectedExits($expectedExits);
    }

    /**
     * @param string[] $arguments
     * @return Token[]
     * @throws InvalidArgumentException
     */
    private function cleanArguments(array $arguments)
    {
        if (!count($arguments)) {
            throw new InvalidArgumentException("Command name is required.");
        }
        $createToken = function ($value) {
            return new Token((string) $value, false);
        };
        return array_map($createToken, $arguments);
    }

    /**
     * @param int|array $expectedExits
     * @return int[]
     * @throws InvalidArgumentException
     */
    private function cleanExpectedExits($expectedExits)
    {
        if (!is_array($expectedExits)) {
            return $this->cleanExpectedExits([$expectedExits]);
        }

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
     * @return Token[]
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

    /**
     * このコマンドと引数のコマンドをパイプで連結し、結果を新しい Command オブジェクトとして返します。
     *
     * @param Command $next 連結先のコマンド
     * @return Command
     */
    public function pipeTo(Command $next)
    {
        $result = clone $next;
        $result->arguments = array_merge($this->arguments, [new Token("|", true)], $next->arguments);
        return $result;
    }
}
