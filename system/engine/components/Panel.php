<?php

namespace VoidEngine;

class Panel extends Control
{
    public function __construct (Control $parent = null)
    {
        parent::__construct ($parent, self::class);
    }
}

?>
