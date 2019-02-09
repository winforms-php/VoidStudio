<?php

namespace VoidEngine;

class MessageBox extends WFClass
{
    public function __construct ()
    {
        parent::__construct ('System.Windows.Forms.MessageBox');
    }

    public function show (...$args): int
    {
        return $this->__call ('Show', $args);
    }
}

?>
