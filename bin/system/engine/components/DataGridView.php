<?php

namespace VoidEngine;

class DataGridView extends Control
{
    protected $columns;
    protected $rows;

    public function __construct (Control $parent = null)
    {
        parent::__construct ($parent, self::class);

        $this->columns = new Items ($this->getProperty ('Columns'));
        $this->rows    = new Items ($this->getProperty ('Rows'));
    }

    public function get_columns ()
    {
        return $this->columns;
    }

    public function get_rows ()
    {
        return $this->rows;
    }
}

?>
