<?php

namespace VoidEngine;

EngineAdditions::loadModule ('FastColoredTextBox.dll');

class FastColoredTextBox extends NoVisual
{
	public function __construct (Control $parent = null)
	{
		$this->selector = VoidEngine::createObject (new ObjectType ('FastColoredTextBoxNS.FastColoredTextBox', 'FastColoredTextBox', true));
		Components::addComponent ($this->selector, $this);
        
		if ($parent)
			$this->parent = $parent;
	}
}

?>
