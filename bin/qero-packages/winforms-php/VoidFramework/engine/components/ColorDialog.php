<?php

namespace VoidEngine;

class ColorDialog extends CommonDialog
{
    public $class = 'System.Windows.Forms.ColorDialog';

    public function __construct ()
    {
        parent::__construct ($this->class);
    }
	
    public function get_color ()
    {
        return $this->getProperty (['Color', 'color']);
    }
	
    public function set_color (int $color)
    {
        $this->setProperty ('Color', $color);
    }
}
