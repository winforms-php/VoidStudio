<?php

namespace VoidEngine;

class PropertyValueChangedEventArgs extends EventArgs
{
    public function get_changedItem ()
    {
        return $this->getProperty ('ChangedItem');
    }
	
    public function get_oldValue ()
    {
        return $this->getProperty ('OldValue');
    }
}

?>
