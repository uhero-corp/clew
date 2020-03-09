<?php

namespace Clew;

abstract class Executor
{
    /**
     * @return Executor
     */
    public static final function getInstance($encoding = null)
    {
        return (substr(PHP_OS, 0, 3) === "WIN") ? self::getWindowsInstance($encoding) : self::getDefaultInstance($encoding);
    }

    /**
     * @param string $encoding
     * @return DefaultExecutor
     * @codeCoverageIgnore
     */
    private static function getDefaultInstance($encoding)
    {
        $format = DefaultArgumentFormat::getInstance();
        $tmpdir = "/tmp/clew";
        return new DefaultExecutor($format, $tmpdir, $encoding);
    }

    /**
     * @param string $encoding
     * @return DefaultExecutor
     * @codeCoverageIgnore
     */
    private static function getWindowsInstance($encoding)
    {
        $format = WindowsArgumentFormat::getInstance();
        $tmpdir = "C:/Temp/clew";
        return new DefaultExecutor($format, $tmpdir, $encoding);
    }

    /**
     * @param Command $command
     * @return CommandResult
     * @throws CommandException コマンドが意図しない終了ステータスを返した場合
     */
    abstract public function execute(Command $command);

    /**
     * 引数で指定された配列をコマンドライン引数として外部コマンドを実行します。
     *
     * このメソッドは execute() のシンタックスシュガーとして機能します。
     * このメソッドに指定された引数をコンストラクタ引数として新しい Command オブジェクトを生成し、
     * そのオブジェクトを引数として execute() メソッドを呼び出します。
     *
     * このメソッドではパイプやリダイレクトを含む複合的なコマンドを実行することはできません。
     *
     * @param string[] $arguments コマンド名とそれに続く引数の一覧
     * @param int[] $expectedExits 想定された終了ステータスの一覧 (整数または配列)
     * @return CommandResult
     */
    public final function executeArray(array $arguments, $expectedExits = [])
    {
        return $this->execute(new Command($arguments, $expectedExits));
    }
}
