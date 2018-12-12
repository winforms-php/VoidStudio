<?php

namespace VoidEngine;

VoidEngine::loadModule ('ScintillaNET.dll');

class Scintilla extends Control
{
    public function __construct (Control $parent = null)
	{
        $this->componentSelector = VoidEngine::createObject (new WFObject ('ScintillaNET.Scintilla', 'ScintillaNET', true));
        Components::addComponent ($this->componentSelector, $this);
        
		if ($parent instanceof Control)
			$this->set_parent ($parent);
    }
}

?>
