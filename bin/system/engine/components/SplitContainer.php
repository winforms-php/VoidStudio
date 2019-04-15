<?php

namespace VoidEngine;

class SplitContainer extends NoVisual
{
    public $class = 'System.Windows.Forms.SplitContainer';

    protected $panel1;
    protected $panel2;

    public function __construct (Component $parent = null)
	{
        parent::__construct ($parent, $this->class);

        $this->panel1 = new SplitterPanel ($this->getProperty ('Panel1'));
        $this->panel2 = new SplitterPanel ($this->getProperty ('Panel2'));
	}
}

class SplitterPanel extends Control
{
    public function __construct (int $selector)
    {
        $this->selector = $selector;
    }
}
