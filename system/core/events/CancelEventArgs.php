<?php

namespace VoidEngine;

class CancelEventArgs extends EventArgs
{
    public function get_cancel ()
    {
        return $this->getProperty ('Cancel');
    }
	
    public function set_cancel (bool $cancel)
    {
        $this->setProperty ('Cancel', $cancel);
    }
}

?>
