<?php

namespace VoidEngine;

class Timer extends Component
{
    public function __construct ()
    {
        parent::__construct (self::class);
    }

    public function get_interval ()
    {
        return $this->getProperty ('Interval');
    }
	
    public function set_interval (int $interval)
    {
        $this->setProperty ('Interval', $interval);
    }
	
    public function get_enabled ()
    {
        return $this->getProperty ('Enabled');
    }
	
    public function set_enabled (bool $enabled)
    {
        $this->setProperty ('Enabled', $enabled);
    }
	
    public function start ()
    {
        $this->callMethod ('Start');
    }
	
    public function stop ()
    {
        $this->callMethod ('Stop');
    }
}

?>
