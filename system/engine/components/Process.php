<?php

namespace VoidEngine;

class Process extends Component
{
	public function __construct (int $pid = null)
	{
        $obj = new ObjectType ('System.Diagnostics.Process');
        $obj->token = 'b77a5c561934e089';

		if ($pid !== null)
		{
            $obj = VoidEngine::createClass ($obj);
            
            $this->selector = ($pid == getmypid ()) ?
                VoidEngine::callMethod ($obj, 'GetCurrentProcess') :
                VoidEngine::callMethod ($obj, 'GetProcessById', $pid);
        }
        
		else $this->selector = VoidEngine::createObject ($obj);

		Components::addComponent ($this->selector, $this);
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
