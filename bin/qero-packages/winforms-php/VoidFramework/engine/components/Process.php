<?php

namespace VoidEngine;

class Process extends Component
{
	public $class 	  = 'System.Diagnostics.Process';
	public $namespace = 'System';

	public function __construct (int $pid = null)
	{
        $this->selector = VoidEngine::createClass ($this->class, $this->namespace);

		if ($pid !== null)
            $this->selector = $pid == getmypid () ?
                VoidEngine::callMethod ($this->selector, 'GetCurrentProcess') :
                VoidEngine::callMethod ($this->selector, 'GetProcessById', $pid);

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
