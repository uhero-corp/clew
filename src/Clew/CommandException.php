<?php

namespace Clew;

use Exception;

class CommandException extends Exception
{
    /**
     * @var string
     */
    private $commandLine;

    /**
     * @var CommandResult
     */
    private $result;

    /**
     * @param string $commandLine
     */
    public function setCommandLine($commandLine)
    {
        $this->commandLine = $commandLine;
    }

    /**
     * @return string
     */
    public function getCommandLine()
    {
        return $this->commandLine;
    }

    /**
     * @param CommandResult $result
     */
    public function setCommandResult(CommandResult $result)
    {
        $this->result = $result;
    }

    /**
     * @return CommandResult
     */
    public function getCommandResult()
    {
        return $this->result;
    }
}
