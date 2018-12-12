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
}

?>
