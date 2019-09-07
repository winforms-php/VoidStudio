<?php

namespace VoidEngine;

class VoidStudioDebugger
{
    public WFObject $process;
    protected int $lastTimestamp = 0;

    public function __construct (WFObject $process)
    {
        if ($process->getType ()->toString () == 'System.Diagnostics.Process')
            $this->process = $process;

        else throw new \Exception ('$process argument must be an "Process" object');
    }

    public function dump (string $savePath, string $properties = '', bool $waitForExit = true)
    {
        $process = run ('"'. APP_DIR .'/system/procdump/procdump.exe"', $properties .' '. $this->process->id .' "'. filepathNoExt ($savePath) .'"');

        if ($waitForExit)
            while (!$process->hasExited)
                usleep (200);
    }

    public function debugRequest (string $command, array $arguments = [])
    {
        file_put_contents (VoidStudioProjectManager::$projectPath .'/build/__debug_request', serialize ([
            'timestamp' => time (),
            'command'   => $command,
            'arguments' => $arguments
        ]));
    }

    public function readDebugAnswer (bool $wait = false)
    {
        $file = VoidStudioProjectManager::$projectPath .'/build/__debug_answer';

        if ($wait)
            while (!file_exists ($file))
                usleep (100);

        if (file_exists ($file))
        {
            $answer = unserialize (file_get_contents ($file));
            unlink ($file);

            if ($answer['timestamp'] > $this->lastTimestamp)
            {
                $this->lastTimestamp = $answer['timestamp'];

                return $answer['data'];
            }
        }

        return false;
    }
}
