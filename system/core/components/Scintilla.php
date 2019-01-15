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

    public function resetSyntax ()
    {
        $this->callMethod ('StyleResetDefault');
    }

    public function clearSyntax ()
    {
        $this->callMethod ('StyleClearAll');
    }

    public function setKeywords (int $index, string $keywords)
    {
        $this->callMethod ('SetKeywords', $index, 'int', $keywords, 'string');
    }

    public function propertyInit (string $propertyName, $propertyValue)
    {
        $this->callMethod ('SetProperty', $propertyName, 'string', $propertyValue, 'string');
    }

    public function set_syntax ($syntax)
    {
        if (file_exists ($syntax))
            $syntax = file_get_contents ($syntax);

        $syntax = json_decode ($syntax, true);

        if (
            !is_array ($syntax['syntax']) ||
            !is_array ($syntax['references']) ||
            !isset ($syntax['lexer'])
        ) return false;

        else
        {
            $this->resetSyntax ();
            $this->clearSyntax ();
            
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

            if (is_array ($syntax['keywords']))
                foreach ($syntax['keywords'] as $id => $keywords)
                    $this->setKeywords ($id, $keywords);

            $this->propertyInit ('fold', 1);
            $this->propertyInit ('fold.compact', 1);

            return true;
        }
    }
}

?>
