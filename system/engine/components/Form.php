<?php

namespace VoidEngine;

class Form extends Control
{
	public function __construct (Control $parent = null)
	{
        parent::__construct (null, self::class);
        
		if ($parent)
			$this->parent = $parent;
	}

	public function get_icon ()
	{
		return new FormIcon ($this->selector);
	}
	
	public function get_clientSize ()
	{
		$obj = $this->getProperty ('ClientSize');

		$w = VoidEngine::getProperty ($obj, 'Width');
		$h = VoidEngine::getProperty ($obj, 'Height');
		
		VoidEngine::removeObject ($obj);
		
		return [$w, $h];
	}
	
	public function set_clientSize (array $size)
	{
		$obj = $this->getProperty ('ClientSize');

		VoidEngine::setProperty ($obj, 'Width', array_shift ($size));
		VoidEngine::setProperty ($obj, 'Height', array_shift ($size));

		$this->setProperty ('ClientSize', $obj);

		VoidEngine::removeObject ($obj);
	}
}

class FormIcon extends Icon
{
    protected $formSelector;

    public function __construct (int $formSelector)
    {
        $this->formSelector = $formSelector;
    }

    public function loadFromFile (string $file)
	{
        $icon = new ObjectType ('System.Drawing.Icon');
        $icon->token = 'b03f5f7f11d50a3a';

		$icon = VoidEngine::createObject ($icon, text ($file));
        
        VoidEngine::setProperty ($this->formSelector, 'Icon', $icon);

		if (!isset ($this->selector))
		    $this->selector = $icon;
	}
}

?>
