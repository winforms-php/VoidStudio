<?php

namespace VoidEngine;

/*
    Класс, отвечающий за работу с аргументами событий попытки закрытия формы
*/

class FormClosingEventArgs extends CancelEventArgs
{
    public function get_closeReason ()
    {
        return $this->getProperty (['CloseReason', 'int']);
    }
}

?>
