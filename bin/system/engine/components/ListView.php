<?php

namespace VoidEngine;

class ListView extends Control
{
    public $class = 'System.Windows.Forms.ListView';
}

class ListViewItem extends Control
{
    public $class = 'System.Windows.Forms.ListViewItem';

    public function __construct (string $text = '')
    {
        parent::__construct (null, $this->class);

        $this->text = $text;
    }
}

class ColumnHeader extends Control
{
    public $class = 'System.Windows.Forms.ColumnHeader';

    public function __construct (string $text = '')
    {
        parent::__construct (null, $this->class);

        $this->text = $text;
    }
}

class ListViewGroup extends Control
{
    public $class = 'System.Windows.Forms.ListViewGroup';

    public function __construct (string $text = '')
    {
        parent::__construct (null, $this->class);

        $this->header = $text;
    }
}
