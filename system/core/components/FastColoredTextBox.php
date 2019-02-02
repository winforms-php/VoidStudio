<?php

namespace VoidEngine;

EngineAdditions::loadModule ('FastColoredTextBox.dll');

class FastColoredTextBox extends NoVisual
{
	public function __construct (Control $parent = null)
	{
        $this->componentSelector = VoidEngine::createObject (new WFObject ('FastColoredTextBoxNS.FastColoredTextBox', 'FastColoredTextBox', true));
        Components::addComponent ($this->componentSelector, $this);
        
		if ($parent instanceof Control)
			$this->set_parent ($parent);
	}
	
    public function get_readOnly ()
    {
        return $this->getProperty ('ReadOnly');
    }
	
    public function set_readOnly (bool $readOnly)
    {
        $this->setProperty ('ReadOnly', $readOnly);
    }

    public function get_wordWrap ()
    {
        return $this->getProperty ('WordWrap');
    }

    public function set_wordWrap (bool $wordWrap)
    {
        return $this->setProperty ('WordWrap', $wordWrap);
    }
	
    public function get_language ()
    {
        return $this->getProperty ('Language');
    }
	
    public function set_language (int $language)
    {
        $this->setProperty ('Language', $language);
    }
}

?>
