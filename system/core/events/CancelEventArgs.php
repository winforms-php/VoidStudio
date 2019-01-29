<?php

namespace VoidEngine;

/*
    Класс, отвечающий за работу с аргументами отмены...
*/

class CancelEventArgs extends EventArgs
{
    public function get_cancel ()
    {
        return $this->getProperty (['Cancel', 'bool']);
    }
	
    public function set_cancel (bool $cancel)
    {
        $this->setProperty ('Cancel', [$cancel, 'bool']);
    }
}

?>
