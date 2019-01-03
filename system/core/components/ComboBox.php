<?php

namespace VoidEngine;

class ComboBox extends Control
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

    public function get_dropDownStyle ()
    {
        return $this->getProperty ('DropDownStyle', 'int');
    }

    public function set_dropDownStyle (int $style)
    {
        $this->setProperty ('DropDownStyle', $style, 'int');
    }
	
    public function get_selectedItem ()
    {
        return $this->getProperty ('SelectedItem', 'string');
    }

    public function set_selectedItem (string $item)
    {
        $this->setProperty ('SelectedItem', $item, 'string');
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
        VoidEngine::removeObject ($this->getProperty ('Items', 'object'));
        unset ($this->items);
        
        parent::dispose ();
	}
}

?>
