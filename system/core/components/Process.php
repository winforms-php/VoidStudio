<?php

namespace VoidEngine;

class Process extends Component
{
	public function __construct (int $pid = null)
	{
        $obj = new WFObject ('System.Diagnostics.Process', 'System');
        $obj->token = 'b77a5c561934e089';

		if ($pid !== null)
		{
            $obj = VoidEngine::buildObject ($obj);
            
            $this->componentSelector = ($pid == getmypid ()) ?
                VoidEngine::callMethod ($obj, 'GetCurrentProcess') :
                VoidEngine::callMethod ($obj, 'GetProcessById', $pid);
        }
        
		else $this->componentSelector = VoidEngine::createObject ($obj);
		
		Components::addComponent ($this->componentSelector, $this);
	}
	
    public function get_processName ()
    {
        return $this->getProperty ('ProcessName');
    }
	
    public function get_mainWindowTitle ()
    {
        return $this->getProperty ('MainWindowTitle');
    }
	
    public function get_id (): int
    {
        return $this->getPrperty ('Id');
    }
	
    public function get_exitCode (): int
    {
        return $this->getProperty ('ExitCode');
    }
	
    public function get_handle ()
    {
        return $this->getProperty ('Handle');
    }
	
    public function get_handleCount (): int
    {
        return $this->getProperty ('HandleCount');
    }
	
    public function get_hasExited (): bool
    {
        return $this->getProperty ('HasExited');
    }
	
    public function start (): bool
    {
        return $this->callMethod ('Start');
    }
	
    public function kill ()
    {
        $this->callMethod ('Kill');
    }
	
	public static function getProcessById (int $pid)
	{
		return new self ($pid);
	}
	
	public static function getCurrentProcess ()
	{
		return new self (getmypid ());
	}
}

?>
