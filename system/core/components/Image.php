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

class Bitmap extends Component
{
    public function __construct (string $filename)
    {
        $bitmap = new WFObject ('System.Drawing.Bitmap', 'System.Drawing');
        $bitmap->token = 'b03f5f7f11d50a3a';

        $this->componentSelector = VoidEngine::createObject ($bitmap, [$filename, 'string']);
        Components::addComponent ($this->componentSelector, $this);
    }
}

?>
