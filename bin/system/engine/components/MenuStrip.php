<?php

namespace VoidEngine;

class MenuStrip extends Component
{
    public $class = 'System.Windows.Forms.MenuStrip';
}

class ContextMenuStrip extends Component
{
    public $class = 'System.Windows.Forms.ContextMenuStrip';
}

class ToolStripMenuItem extends Control
{
    public $class = 'System.Windows.Forms.ToolStripMenuItem';

    protected $items;

    public function __construct (string $text = '')
    {
        parent::__construct (null, $this->class);

        $this->text  = $text;
        $this->items = new WFObject ($this->getProperty ('DropDownItems'));
    }
}
