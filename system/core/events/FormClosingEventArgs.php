<?php

namespace VoidEngine;

class FormClosingEventArgs extends CancelEventArgs
{
    public function get_closeReason ()
    {
        return $this->getProperty ('CloseReason');
    }
}

?>
