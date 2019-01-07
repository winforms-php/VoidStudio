<?php

namespace VoidEngine;

class PropertyGridEx extends Control
{
    protected $items;

    public function __construct (Control $parent = null)
    {
        $this->componentSelector = VoidEngine::createObject (new WFObject ('PropertyGridEx.PropertyGridEx', false, true));
        Components::addComponent ($this->componentSelector, $this);

        $this->items = $this->getProperty ('Item', 'object');
        
		if ($parent instanceof Control)
			$this->set_parent ($parent);
    }

    public function addItem (string $name, string $comment, $value, string $category, string $description)
    {
        VoidEngine::callMethod ($this->items, 'Add', $name, 'string', $comment, 'string', $value, getLogicalVarType ($value), $category, 'string', $description, 'string', true, 'bool');
    }

    public function get_selectedObject ()
    {
        return $this->getProperty ('SelectedObject', 'object');
    }

    public function set_selectedObject (int $selector)
    {
        $this->setProperty ('SelectedObject', $selector, 'object');
    }

    public function refresh ()
    {
        $this->callMethod ('Refresh');
    }
}

?>
