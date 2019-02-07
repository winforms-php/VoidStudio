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

    public function get_selected ()
    {
        $index = $this->getProperty ('SelectedIndex');

        return ($index >= 0 && $index < $this->items->count) ? 
            $this->items[$index] : false;
    }

    public function get_items ()
    {
        return $this->items;
    }

    public function get_itemHeight ()
    {
        return $this->getProperty ('ItemHeight');
    }

    public function set_itemHeight (int $height)
    {
        $this->setProperty ('ItemHeight', $height);
    }
}

class CheckedListBox extends ListBox {}

?>
