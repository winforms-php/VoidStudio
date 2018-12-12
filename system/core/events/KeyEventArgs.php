<?php

namespace VoidEngine;

/*
    Класс, отвечающий за работу с аргументами событий клавиатуры
*/

class KeyEventArgs extends EventArgs
{
    public function get_alt ()
    {
        return $this->getProperty ('Alt', 'bool');
    }
	
    public function get_control ()
    {
        return $this->getProperty ('Control', 'bool');
    }
	
	public function get_handled ()
    {
        return $this->getProperty ('Handled', 'bool');
    }
	
    public function set_handled (bool $handled)
    {
        $this->setProperty ('Handled', $handled, 'bool');
    }
	
    public function get_keyCode ()
    {
        return $this->getProperty ('KeyCode', 'int');
    }
	
    public function get_keyData ()
    {
        return $this->getProperty ('KeyData', 'int');
    }
	
    public function get_keyValue ()
    {
        return $this->getProperty ('KeyValue', 'int');
    }
	
    public function get_modifiers ()
    {
        return $this->getProperty ('Modifiers', 'int');
    }
	
    public function get_shift ()
    {
        return $this->getProperty ('Shift', 'bool');
    }
	
    public function get_suppressKeyPress ()
    {
        return $this->getProperty ('SuppressKeyPress', 'bool');
    }
	
    public function set_suppressKeyPress (bool $suppressKeyPress)
    {
        $this->setProperty ('SuppressKeyPress', $suppressKeyPress, 'bool');
    }
}

?>
