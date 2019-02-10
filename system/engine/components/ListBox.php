<?php

namespace VoidEngine;

class ListBox extends Control
{
    protected $items;

    public function __construct (Control $parent = null)
	{
        parent::__construct ($parent, self::class);

        $this->items = new Items ($this->getProperty ('Items'));
    }
    
    public function get_selectedItems ()
    {
        return new Items ($this->getProperty ('SelectedItems'));
    }
}

class CheckedListBox extends ListBox {}

?>
