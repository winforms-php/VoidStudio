<?php

namespace VoidEngine;

class Label extends Control
{
    public function __construct (Control $parent = null)
    {
        parent::__construct ($parent, self::class);
    }

    public function get_textAlign ()
    {
        return $this->getProperty ('TextAlign', 'int');
    }

    public function set_textAlign (int $textAlign)
    {
        $this->setProperty ('TextAlign', $textAlign, 'int');
    }
}

?>
