<?php

namespace VoidEngine;

class MouseEventArgs extends EventArgs
{
    public function get_button ()
    {
        return $this->getProperty ('Button');
    }
	
    public function get_clicks ()
    {
        return $this->getProperty ('Clicks');
    }
	
    public function get_delta ()
    {
        return $this->getProperty ('Delta'); 
    }
	
    public function get_x ()
    {
        return $this->getProperty ('X');
    }
	
    public function get_y ()
    {
        return $this->getProperty ('Y');
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
