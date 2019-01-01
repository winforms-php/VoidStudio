<?php

namespace VoidEngine;

class ListBox extends Control
{
    protected $items;

    public function __construct (Control $parent = null)
	{
        parent::__construct ($parent, self::class);

        $this->items = new Items ($this->getProperty ('Items', 'object'));
    }
    
    public function get_items ()
    {
        return $this->items;
    }
}

?>