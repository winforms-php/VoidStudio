<?php

namespace VoidEngine;

class ListView extends Control
{
    protected $items;
    protected $columns;
    protected $smallImagesList;
    protected $largeImagesList;

    public function __construct (Control $parent = null)
    {
        parent::__construct ($parent, self::class);

        $this->items   = new Items ($this->getProperty ('Items'));
        $this->columns = new Items ($this->getProperty ('Columns'));

        $this->smallImagesList = new ImageList;
        $this->largeImagesList = new ImageList;

        // $this->setProperty ('SmallImageList', [$this->smallImagesList->selector, 'object']);
        // $this->setProperty ('LargeImageList', [$this->largeImagesList->selector, 'object']);
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
