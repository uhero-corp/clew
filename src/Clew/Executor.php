<?php

namespace Clew;

abstract class Executor
{
    /**
     * @return Executor
     */
    public static final function getInstance()
    {
        return (substr(PHP_OS, 0, 3) === "WIN") ? self::getWindowsInstance() : self::getDefaultInstance();
    }

    /**
     * @return DefaultExecutor
     */
    private static function getDefaultInstance()
    {
        static $instance = null;
        if ($instance === null) {
            $format   = DefaultArgumentFormat::getInstance();
            $tmpdir   = "/tmp/clew";
            $instance = new DefaultExecutor($format, $tmpdir);
        }
        return $instance;
    }

    /**
     * @return DefaultExecutor
     */
    private static function getWindowsInstance()
    {
        static $instance = null;
        if ($instance === null) {
            $format   = WindowsArgumentFormat::getInstance();
            $tmpdir   = "C:/Temp/clew";
            $instance = new DefaultExecutor($format, $tmpdir);
        }
        return $instance;
    }

    /**
     * @param Command $command
     * @return CommandResult
     * @throws CommandException コマンドが意図しない終了ステータスを返した場合
     */
    abstract public function execute(Command $command);
}
