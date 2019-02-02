<?php

namespace VoidEngine;

class VideoBox extends NoVisual
{
	public function __construct (Control $parent = null)
	{
        $this->componentSelector = VoidEngine::createObject (new WFObject ('WinForms_PHP.Video', false, true));
        Components::addComponent ($this->componentSelector, $this);
        
		if ($parent instanceof Control)
			$this->set_parent ($parent);
	}
	
    public function open (string $file)
    {
        $this->callMethod ('Open', $file);
    }

    public function play ()
    {
        $this->callMethod ('Play');
    }
	
    public function pause ()
    {
        $this->callMethod ('Pause');
    }
	
    public function stop ()
    {
        $this->callMethod ('Stop');
    }
	
    public function get_volume ()
    {
        return $this->getProperty ('Volume');
    }
	
    public function set_volume (int $volume)
    {
        $this->setProperty ('Volume', $volume);
    }
	
    public function get_duration ()
    {
        return $this->getProperty ('Duration');
    }
	
    public function get_position ()
    {
        return $this->getProperty ('CurrentPosition');
    }
	
    public function set_position ($position) // float
    {
        $this->setProperty ('CurrentPosition', (float) $position);
    }
}

?>
