<?php

namespace VoidEngine;

class FormClosedEventArgs extends EventArgs
{
    public function get_closeReason ()
    {
        return $this->getProperty ('CloseReason');
    }
}

?>
