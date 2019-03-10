<?php

namespace VoidEngine;

class ToolStrip extends Control
{
    protected $items;

    public function __construct (Control $parent = null)
	{
        parent::__construct ($parent, self::class);

        $this->items = new Items ($this->getProperty ('Items'));
    }
    
    public function get_displayedItems ()
    {
        return new Items ($this->getProperty ('DisplayedItems'));
    }
}

?>
