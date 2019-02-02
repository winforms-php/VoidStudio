<?php

namespace VoidEngine;

class KeyPressEventArgs extends EventArgs
{
    public function get_handled ()
    {
        return $this->getProperty ('Handled');
    }
	
    public function set_handled (bool $handled)
    {
        $this->setProperty ('Handled', $handled);
    }

    public function get_keyChar ()
    {
        return $this->getProperty ('KeyChar');
    }
	
    public function set_keyChar (string $char)
    {
        $this->setProperty ('KeyChar', $char);
    }
}

?>
