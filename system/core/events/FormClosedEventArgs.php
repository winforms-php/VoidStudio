<?php

namespace VoidEngine;

/*
    Класс, отвечающий за работу с аргументами событий закрытия формы
*/

class FormClosedEventArgs extends EventArgs
{
    public function get_closeReason ()
    {
        return $this->getProperty ('CloseReason', 'int');
    }
}

?>
