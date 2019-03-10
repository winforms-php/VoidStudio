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

class Icon extends WFObject
{
    public function __construct (string $file)
    {
        $icon = new ObjectType ('System.Drawing.Icon');
        $icon->token = 'b03f5f7f11d50a3a';

		parent::__construct ($icon);
    }

    public function applyToObject (int $selector): void
	{
		VoidEngine::setProperty ($selector, 'Icon', $this->selector);
	}
	
	public function saveToFile (string $file): void
	{
		VoidEngine::callMethod ($this->selector, 'Save', $file);
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
