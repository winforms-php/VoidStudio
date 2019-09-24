<?php

namespace VoidEngine;

class Form extends Control
{
	public $class = 'System.Windows.Forms.Form';

	public function get_icon ()
	{
		return new FormIcon ($this->selector);
	}
	
	public function get_clientSize ()
	{
		$size = $this->getProperty ('ClientSize');
		
		return [
			\VoidCore::getProperty ($size, 'Width'),
			\VoidCore::getProperty ($size, 'Height')
		];
	}
	
	public function set_clientSize ($size)
	{
		if (is_array ($size))
		{
			$clientSize = $this->getProperty ('ClientSize');

			\VoidCore::setProperty ($clientSize, 'Width', array_shift ($size));
			\VoidCore::setProperty ($clientSize, 'Height', array_shift ($size));

			$this->setProperty ('ClientSize', $clientSize);
		}

		else $this->setProperty ('ClientSize', EngineAdditions::uncoupleSelector ($size));
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
        $icon = \VoidCore::createObject ('System.Drawing.Icon', 'System.Drawing', $file);
        
        \VoidCore::setProperty ($this->formSelector, 'Icon', $icon);

		if (!isset ($this->selector))
		    $this->selector = $icon;
	}
}
