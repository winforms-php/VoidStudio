<?php

namespace VoidEngine;

VoidEngine::loadModule ('ScintillaNET.dll');

class Scintilla extends NoVisual
{
    public function __construct (Control $parent = null)
	{
        $this->componentSelector = VoidEngine::createObject (new WFObject ('ScintillaNET.Scintilla', 'ScintillaNET', true));
        Components::addComponent ($this->componentSelector, $this);
        
		if ($parent instanceof Control)
			$this->set_parent ($parent);
    }

    public function get_lexer ()
    {
        return $this->getProperty ('Lexer', 'int');
    }

    public function set_lexer (int $lexer)
    {
        $this->setProperty ('Lexer', $lexer, 'int');
    }

    public function get_styles ()
    {
        return new Items ($this->getProperty ('Styles', 'object'));
    }
}

?>
