<?php

namespace VoidEngine;

class SplitContainer extends NoVisual
{
    protected $panel1;
    protected $panel2;

    public function __construct (Control $parent = null)
	{
        parent::__construct ($parent, self::class);

        $this->panel1 = new SplitterPanel ($this->getProperty ('Panel1'));
        $this->panel2 = new SplitterPanel ($this->getProperty ('Panel2'));
	}
}

class SplitterPanel extends Control
{
    public function __construct (int $selector)
    {
        $this->componentSelector = $selector;
        $this->componentClass    = self::class;
        
        Components::addComponent ($selector, $this);
    }
}

?>
