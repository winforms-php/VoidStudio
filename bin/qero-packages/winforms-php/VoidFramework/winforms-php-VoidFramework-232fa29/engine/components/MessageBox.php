<?php

namespace VoidEngine;

class MessageBox extends WFClass
{
    public $class = 'System.Windows.Forms.MessageBox';

    public function __construct ()
    {
        parent::__construct ($this->class);
    }
}
