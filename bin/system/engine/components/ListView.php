<?php

namespace VoidEngine;

class ListView extends Control
{
    protected $items;
    protected $columns;
    protected $groups;

    public function __construct (Control $parent = null)
    {
        parent::__construct ($parent, self::class);

        $this->items   = new Items ($this->getProperty ('Items'));
        $this->columns = new Items ($this->getProperty ('Columns'));
        $this->groups  = new Items ($this->getProperty ('Groups'));
    }

    public function get_selectedItems ()
    {
        return new Items ($this->getProperty ('SelectedItems'));
    }
}

class ListViewItem extends Control
{
    protected $subItems;

    public function __construct (string $text = '')
    {
        parent::__construct (null, self::class);

        $this->text     = $text;
        $this->subItems = new Items ($this->getProperty ('SubItems'));
    }
}

class ColumnHeader extends Control
{
    public function __construct (string $text = '')
    {
        parent::__construct (null, self::class);

        $this->text = $text;
    }
}

class ListViewGroup extends Control
{
    public function __construct (string $text = '')
    {
        parent::__construct (null, self::class);

        $this->header = $text;
    }
}

class ImageList extends Component
{
    protected $images;

    public function __construct ()
    {
        parent::__construct (self::class);

        $this->images = new Items ($this->getProperty ('Images'));
    }
}

?>
