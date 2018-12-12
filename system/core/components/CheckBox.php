<?php

namespace VoidEngine;

class CheckBox extends Control
{
	public function __construct (Control $parent = null)
	{
        parent::__construct ($parent, self::class);
	}
	
    public function get_checked ()
    {
        return $this->getProperty ('Checked', 'bool');
    }
	
    public function set_checked (bool $checked)
    {
        $this->setProperty ('Checked', $checked, 'bool');
    }
}

?>
