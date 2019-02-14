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
}

?>
