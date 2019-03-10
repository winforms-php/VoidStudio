<?php

namespace VoidEngine;

class GroupBox extends Control
{
    public function __construct (Control $parent = null)
    {
        parent::__construct ($parent, self::class);
    }
}

?>
