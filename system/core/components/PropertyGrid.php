<?php

namespace VoidEngine;

class PropertyGrid extends Control
{
    public function __construct (Control $parent = null)
    {
        parent::__construct ($parent, self::class);
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

class PropertyGridEx extends PropertyGrid
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

    public function addItem (string $name, $value, bool $readOnly, string $category, string $description, bool $visible = true)
    {
        VoidEngine::callMethod ($this->items, 'Add', $name, 'string', $value, getLogicalVarType ($value), $readOnly, 'bool', $category, 'string', $description, 'string', $visible, 'bool');
    }
}

?>
