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
}
