<?php

namespace VoidEngine;

VoidEngine::loadModule ('FastColoredTextBox.dll');

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
        return $this->getProperty ('ReadOnly', 'bool');
    }
	
    public function set_readOnly (bool $readOnly)
    {
        $this->setProperty ('ReadOnly', $readOnly, 'bool');
    }

    public function get_wordWrap ()
    {
        return $this->getProperty ('WordWrap', 'bool');
    }

    public function set_wordWrap (bool $wordWrap)
    {
        return $this->setProperty ('WordWrap', $wordWrap, 'bool');
    }
	
    public function get_language ()
    {
        return $this->getProperty ('Language', 'int');
    }
	
    public function set_language (int $language)
    {
        $this->setProperty ('Language', $language, 'int');
    }
}

?>
