<?php

namespace VoidEngine;

class VScrollBar extends Control
{
    public function __construct (Control $parent = null)
	{
        trigger_error ('Component "VScrollBar" is deprecated');

        parent::__construct ($parent, self::class);

        if ($parent instanceof Control)
        {
            $this->parent = $parent;
            $this->dock   = dsRight;

            //$parent->setProperty ('ScrollBars', $this->componentSelector, 'object');
        }
    }
}

?>
