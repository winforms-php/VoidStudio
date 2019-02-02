<?php

namespace VoidEngine;

class Image extends WFClass
{
    public function __construct ()
    {
        $image = new WFObject ('System.Drawing.Image', 'System.Drawing');
        $image->token = 'b03f5f7f11d50a3a';

        parent::__construct ($image);
    }

    public function loadFromFile (string $path)
    {
        return $this->__call ('FromFile', [$path]);
    }
}

?>
