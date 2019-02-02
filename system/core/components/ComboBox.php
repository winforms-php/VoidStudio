<?php

namespace VoidEngine;

class ComboBox extends Control
{
    protected $items;

    public function __construct (Control $parent = null)
    {
        parent::__construct ($parent, self::class);

        $this->items = new Items ($this->getProperty ('Items'));
    }
	
    public function get_items ()
    {
        return $this->items;
    }

    public function get_dropDownStyle ()
    {
        return $this->getProperty ('DropDownStyle');
    }

    public function set_dropDownStyle (int $style)
    {
        $this->setProperty ('DropDownStyle', $style);
    }
	
    public function get_selectedItem ()
    {
        return $this->getProperty ('SelectedItem');
    }

    public function set_selectedItem (string $item)
    {
        $this->setProperty ('SelectedItem', $item);
    }
	
    public function get_selectedIndex ()
    {
        return $this->getProperty ('SelectedIndex');
    }
	
    public function set_selectedIndex (int $index)
    {
        $this->setProperty ('SelectedIndex', $index);
    }
	
	public function dispose ()
	{
        VoidEngine::removeObject ($this->getProperty ('Items'));
        unset ($this->items);
        
        parent::dispose ();
	}
}

?>
