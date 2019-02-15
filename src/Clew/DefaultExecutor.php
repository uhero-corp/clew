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
     * @var string
     */
    private $encoding;

    /**
     * @param ArgumentFormat $format
     * @param string $tmpDir
     * @param string $encoding
     */
    public function __construct(ArgumentFormat $format, $tmpDir, $encoding = null)
    {
        $this->initDir($tmpDir);
        $this->format   = $format;
        $this->tmpDir   = $tmpDir;
        $this->encoding = strlen($encoding) ? $encoding : "UTF-8";
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

        $cmd    = $this->formatCommandLine($command, $log);
        $output = [];
        $exval  = 0;
        exec($cmd, $output, $exval);
        $stdout = implode(PHP_EOL, $output);
        $stderr = $this->fetchStderr($log);
        $result = new CommandResult($stdout, $stderr, $exval);

        if (!$command->validateExitCode($exval)) {
            $e = new CommandException("Unexpected exit status: {$exval}", $exval);
            $e->setCommandLine($cmd);
            $e->setCommandResult($result);
            throw $e;
        }
        return $result;
    }

    /**
     * @param Command $command
     * @param string $log
     * @return string
     */
    private function formatCommandLine(Command $command, $log)
    {
        $format     = $this->format;
        $getArg     = function (Token $t) use ($format) {
            return $t->format($format);
        };
        $escapedLog = $this->format->formatFilePath($log);
        $cmd        = implode(" ", array_map($getArg, $command->getArguments())) . " 2> {$escapedLog}";
        return ($this->encoding === "UTF-8") ? $cmd : mb_convert_encoding($cmd, $this->encoding, "UTF-8");
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
