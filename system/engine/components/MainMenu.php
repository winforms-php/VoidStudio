<?php

namespace VoidEngine;

class MainMenu extends Component
{
    protected $items;

    public function __construct ()
    {
        parent::__construct (self::class);

        $this->items = new Items ($this->getProperty ('MenuItems'));
    }
}

class ContextMenu extends Component
{
    protected $items;

    public function __construct ()
    {
        parent::__construct (self::class);

        $this->items = new Items ($this->getProperty ('MenuItems'));
    }
}

class MenuItem extends Control
{
    protected $items;

    public function __construct (string $text = '')
    {
        parent::__construct (null, self::class);

        $this->text  = $text;
        $this->items = new Items ($this->getProperty ('MenuItems'));
    }
}

?>
