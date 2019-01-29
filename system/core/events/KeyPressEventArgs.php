<?php

namespace VoidEngine;

/*
    Класс, отвечающий за работу с аргументами нажатия клавиши
*/

class KeyPressEventArgs extends EventArgs
{
    public function get_handled ()
    {
        return $this->getProperty (['Handled', 'bool']);
    }
	
    public function set_handled (bool $handled)
    {
        $this->setProperty ('Handled', [$handled, 'bool']);
    }

    public function get_keyChar ()
    {
        return $this->getProperty (['KeyChar', 'string']);
    }
	
    public function set_keyChar (string $char)
    {
        $this->setProperty ('KeyChar', [$char, 'char']);
    }
}

?>
