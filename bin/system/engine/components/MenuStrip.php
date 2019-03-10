<?php

namespace VoidEngine;

class MenuStrip extends Component
{
    protected $items;

    public function __construct ()
    {
        parent::__construct (self::class);

        $this->items = new Items ($this->getProperty ('Items'));
    }
}

class ContextMenuStrip extends Component
{
    protected $items;

    public function __construct ()
    {
        parent::__construct (self::class);

        $this->items = new Items ($this->getProperty ('Items'));
    }
}

class ToolStripDropDownMenu extends Component
{
    protected $items;

    public function __construct ()
    {
        parent::__construct (self::class);

        $this->items = new Items ($this->getProperty ('Items'));
    }
}

class ToolStripMenuItem extends Control
{
    protected $items;

    public function __construct (string $text = '')
    {
        parent::__construct (null, self::class);

        $this->text  = $text;
        $this->items = new Items ($this->getProperty ('DropDownItems'));
    }
}

?>
