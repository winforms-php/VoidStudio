<?php

namespace VoidEngine;

class ColorDialog extends CommonDialog
{
    public function __construct ()
    {
        parent::__construct (__CLASS__);
    }
	
    public function get_color ()
    {
        return $this->getProperty ('Color', 'color');
    }
	
    public function set_color ($color)
    {
        $this->setProperty ('Color', $color, 'color');
    }
	
    public function get_anyColor ()
    { 
        return $this->getProperty ('AnyColor', 'bool');
    }
	
    public function set_anyColor (bool $any)
    {
        $this->setProperty ('AnyColor', $any, 'bool');
    }
	
    public function get_fullOpen ()
    {
        return $this->getProperty ('FullOpen', 'bool');
    }
	
    public function set_fullOpen (bool $full)
    {
        $this->setProperty ('FullOpen', $full, 'bool');
    }
	
    public function get_customColors ()
    {
        return $this->getArrayProperty ('CustomColors', 'int');
    }
}

?>
