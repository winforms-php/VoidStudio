<?php

namespace VoidEngine;

class KeyEventArgs extends EventArgs
{
    public function get_alt ()
    {
        return $this->getProperty ('Alt');
    }
	
    public function get_control ()
    {
        return $this->getProperty ('Control');
    }
	
	public function get_handled ()
    {
        return $this->getProperty ('Handled');
    }
	
    public function set_handled (bool $handled)
    {
        $this->setProperty ('Handled', $handled);
    }
	
    public function get_keyCode ()
    {
        return $this->getProperty ('KeyCode');
    }
	
    public function get_keyData ()
    {
        return $this->getProperty ('KeyData');
    }
	
    public function get_keyValue ()
    {
        return $this->getProperty ('KeyValue');
    }
	
    public function get_modifiers ()
    {
        return $this->getProperty ('Modifiers');
    }
	
    public function get_shift ()
    {
        return $this->getProperty ('Shift');
    }
	
    public function get_suppressKeyPress ()
    {
        return $this->getProperty ('SuppressKeyPress');
    }
	
    public function set_suppressKeyPress (bool $suppressKeyPress)
    {
        $this->setProperty ('SuppressKeyPress', $suppressKeyPress);
    }
}

?>
