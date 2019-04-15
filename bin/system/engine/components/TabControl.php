<?php

namespace VoidEngine;

class TabControl extends Control
{
    public $class = 'System.Windows.Forms.TabControl';

    protected $items;

    public function __construct (Component $parent = null)
	{
        parent::__construct ($parent, $this->class);

        $this->items = new WFObject ($this->getProperty ('TabPages'));
    }
}

class TabPage extends Control
{
    public $class = 'System.Windows.Forms.TabPage';

    public function __construct (string $text = '')
    {
        parent::__construct (null, $this->class);

        $this->text = $text;
    }
}
