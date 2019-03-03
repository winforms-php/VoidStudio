<?php

namespace VoidEngine;

class EventGrid extends NoVisual
{
	public function __construct (Control $parent = null)
    {
        $this->selector = VoidEngine::createObject (new ObjectType ('WinForms_PHP.EventGrid', false, true));
		Components::addComponent ($this->selector, $this);
        
		if ($parent)
			$this->parent = $parent;
    }
}

?>
