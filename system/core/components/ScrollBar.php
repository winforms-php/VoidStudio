<?php

namespace VoidEngine;

class VScrollBar extends Control
{
    public function __construct (Control $parent = null)
	{
        parent::__construct ($parent, self::class);

        if ($parent instanceof Control)
        {
            $this->setProperty ('Parent', $parent->selector, 'object');
            $this->setProperty ('Dock', dsRight, 'int');

            //$parent->setProperty ('ScrollBars', $this->componentSelector, 'object');
        }
    }
}

?>
