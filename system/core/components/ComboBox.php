<?php

namespace VoidEngine;

class ComboBox extends Control
{
    protected $items;

    public function __construct (Control $parent = null)
    {
        parent::__construct ($parent, self::class);

        $this->items = new ComboBoxItems ($this->getProperty ('Items', 'object'));
    }
	
    public function get_items ()
    {
        return $this->items;
    }

    public function get_dropDownStyle ()
    {
        return $this->getProperty ('DropDownStyle', 'int');
    }

    public function set_dropDownStyle (int $style)
    {
        return $this->setProperty ('DropDownStyle', $style, 'int');
    }
	
    public function get_selectedItem ()
    {
        $index = $this->get_selectedItemIndex ();

        return ($index >= 0 && $index < $this->items->count) ?
            $this->items->offsetGet ($this->get_selectedItemIndex ()) :
            false;
    }
	
    public function get_selectedItemIndex ()
    {
        return $this->getProperty ('SelectedIndex', 'int');
    }
	
    public function set_selectedItemIndex (int $index)
    {
        $this->setProperty ('SelectedIndex', $index, 'int');
    }
	
	public function dispose ()
	{
        parent::dispose ();
        
        VoidEngine::removeObject ($this->getProperty ('Items', 'object'));
        unset ($this->items);
	}
}

class ComboBoxItems extends Items {}

?>
