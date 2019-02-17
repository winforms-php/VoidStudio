<?php

namespace VoidEngine;

class Process extends Component
{
	public function __construct (int $pid = null)
	{
        $obj = new ObjectType ('System.Diagnostics.Process', 'System');
		$obj->token = 'b77a5c561934e089';
		
		$this->selector = VoidEngine::createClass ($obj);

		if ($pid !== null)
            $this->selector = $pid == getmypid () ?
                VoidEngine::callMethod ($this->selector, 'GetCurrentProcess') :
                VoidEngine::callMethod ($this->selector, 'GetProcessById', $pid);

		Components::addComponent ($this->selector, $this);
	}

	public function getProcessesByName (string $name)
	{
		return new Items (VoidEngine::callMethod ($this->selector, 'GetProcessesByName', $name));
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
