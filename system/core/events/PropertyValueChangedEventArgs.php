<?php

namespace VoidEngine;

/*
    Класс, отвечающий за работу с аргументами изменения полей PropertyGrid
*/

class PropertyValueChangedEventArgs extends EventArgs
{
    public function get_changedItem ()
    {
        return $this->getProperty (['ChangedItem', 'object']);
    }
	
    public function get_oldValue ()
    {
        return $this->getProperty ('OldValue');
    }
}

?>
