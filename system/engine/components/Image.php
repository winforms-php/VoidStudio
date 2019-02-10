<?php

namespace VoidEngine;

class Image extends WFClass
{
    public function __construct ()
    {
        $image = new ObjectType ('System.Drawing.Image');
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
        $bitmap = new ObjectType ('System.Drawing.Bitmap');
        $bitmap->token = 'b03f5f7f11d50a3a';

        $this->selector = VoidEngine::createObject ($bitmap, [$filename, 'string']);
    }
}

?>
