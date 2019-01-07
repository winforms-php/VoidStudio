<?php

namespace VoidEngine;

VoidEngine::loadModule ('ScintillaNET.dll');

class Scintilla extends NoVisual
{
    protected $styles;

    public function __construct (Control $parent = null)
	{
        $this->componentSelector = VoidEngine::createObject (new WFObject ('ScintillaNET.Scintilla', 'ScintillaNET', true));
        Components::addComponent ($this->componentSelector, $this);

        $this->styles = $this->getProperty ('Styles', 'object');
        
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

    public function set_syntax ($syntax)
    {
        if (file_exists ($syntax))
            $syntax = file_get_contents ($syntax);

        $syntax = json_decode ($syntax, true);

        if (!is_array ($syntax['syntax']) || !is_array ($syntax['references']) || !isset ($syntax['lexer']))
            return false;

        else
        {
            foreach ($syntax['references'] as $name => $value)
                if (isset ($syntax['syntax'][$name]))
                {
                    $element = VoidEngine::getArrayValue ($this->styles, $value, 'object');
                    $color   = $syntax['syntax'][$name];

                    if (defined ($color))
                        $color = constant ($color);
                    
                    VoidEngine::setProperty ($element, 'ForeColor', $color, 'color');
                }

            $this->lexer = $syntax['lexer'];

            return true;
        }
    }
}

?>
