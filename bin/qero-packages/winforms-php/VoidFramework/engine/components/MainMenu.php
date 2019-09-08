<?php

namespace VoidEngine;

class MainMenu extends Component
{
    public $class = 'System.Windows.Forms.MainMenu';

    protected $items;

    public function __construct ()
    {
        parent::__construct ($this->class);

        $this->items = new WFObject ($this->getProperty ('MenuItems'));
    }
}

class ContextMenu extends Component
{
    public $class = 'System.Windows.Forms.ContextMenu';

    protected $items;

    public function __construct ()
    {
        parent::__construct ($this->class);

        $this->items = new WFObject ($this->getProperty ('MenuItems'));
    }
}

class MenuItem extends Control
{
    public $class = 'System.Windows.Forms.MenuItem';

    protected $items;

    public function __construct (string $text = '')
    {
        parent::__construct (null, $this->class);

        $this->text  = $text;
        $this->items = new WFObject ($this->getProperty ('MenuItems'));
    }
}
