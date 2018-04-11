<?php

namespace Clew;

class DefaultExecutor extends Executor
{
    /**
     * @var ArgumentFormat
     */
    private $format;

    /**
     * @var string
     */
    private $tmpDir;

    /**
     * @param ArgumentFormat $format
     * @param string $tmpDir
     */
    public function __construct(ArgumentFormat $format, $tmpDir)
    {
        $this->initDir($tmpDir);
        $this->format = $format;
        $this->tmpDir = $tmpDir;
    }

    /**
     * @param string $d
     * @return bool
     */
    private function initDir($d)
    {
        return is_dir($d) || ($this->initDir(dirname($d)) && mkdir($d) && chmod($d, 0777));
    }

    /**
     * @param Command $command
     * @return CommandResult
     */
    public function execute(Command $command)
    {
        $log = $this->tmpDir . "/stderr.log";
        @unlink($log);

        $escapedLog = $this->format->formatFilePath($log);
        $cmd        = implode(" ", array_map([$this->format, "format"], $command->getArguments())) . " 2> {$escapedLog}";
        $stdout     = [];
        $exval      = 0;
        exec($cmd, $stdout, $exval);
        if (!$command->validateExitCode($exval)) {
            throw new CommandException("Unexpected exit status: {$exval}");
        }

        $stderr = $this->fetchStderr($log);
        return new CommandResult(implode(PHP_EOL, $stdout), $stderr, $exval);
    }

    /**
     * @param string $logFile
     * @return string
     */
    private function fetchStderr($logFile)
    {
        $log = is_file($logFile) ? file_get_contents($logFile) : "";
        return preg_replace("/\\r\\n\\z|\\r\\z|\\n\\z/s", "", $log);
    }
}
