<?php

namespace VoidEngine;

class Timer extends Component
{
    public function __construct ()
    {
        parent::__construct (__CLASS__);
    }

    public function get_interval ()
    {
        return $this->getProperty ('Interval', 'int');
    }
	
    public function set_interval (int $interval)
    {
        $this->setProperty ('Interval', $interval, 'int');
    }
	
    public function get_enabled ()
    {
        return $this->getProperty ('Enabled', 'bool');
    }
	
    public function set_enabled (bool $enabled)
    {
        $this->setProperty ('Enabled', $enabled, 'bool');
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
