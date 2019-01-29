<?php

namespace VoidEngine;

/*
    Класс, отвечающий за работу с аргументами событий мыши
*/

class MouseEventArgs extends EventArgs
{
    public function get_button ()
    {
        return $this->getProperty (['Button', 'string']);
    }
	
    public function get_clicks ()
    {
        return $this->getProperty (['Clicks', 'int']);
    }
	
    public function get_delta ()
    {
        return $this->getProperty (['Delta', 'int']); 
    }
	
    public function get_x ()
    {
        return $this->getProperty (['X', 'int']);
    }
	
    public function get_y ()
    {
        return $this->getProperty (['Y', 'int']);
    }
	
    public function get_location ()
    {
        return [
            $this->get_x (),
            $this->get_y ()
        ];
    }
}

?>
