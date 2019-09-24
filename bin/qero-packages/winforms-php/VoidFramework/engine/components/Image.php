<?php

namespace VoidEngine;

class Image extends WFClass
{
    public $class     = 'System.Drawing.Image';
    public $namespace = 'System.Drawing';

    public function __construct ()
    {
        parent::__construct ($this->class);
    }

    public function loadFromFile (string $path)
    {
        return $this->fromFile ($path);
    }
}

class Icon extends WFObject
{
    public $class     = 'System.Drawing.Icon';
    public $namespace = 'System.Drawing';

    public function __construct (string $file)
    {
        parent::__construct ($this->class);

        $this->fromFile ($file);
    }

    public function applyToObject (int $selector): void
	{
		\VoidCore::setProperty ($selector, 'Icon', $this->selector);
	}
	
	public function saveToFile (string $file): void
	{
		\VoidCore::callMethod ($this->selector, 'Save', $file);
	}
}

class Bitmap extends WFObject
{
    public $class     = 'System.Drawing.Bitmap';
    public $namespace = 'System.Drawing';

    public function __construct (string $filename)
    {
        parent::__construct ($this->class, $this->namespace, [$filename, 'string']);
    }
}
