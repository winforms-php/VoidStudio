<?php

namespace VoidEngine;

class PictureBox extends Control
{
	public $class = 'System.Windows.Forms.PictureBox';

	protected $image;
	
	public function __construct (Component $parent = null)
	{
        parent::__construct ($parent, $this->class);
        
		$this->image = new PictureBoxImage ($this->selector);
	}
}

class PictureBoxImage
{
	protected $selector;
    protected $pictureBoxSelector;
    protected $clipboard;
    
	public function __construct (int $pictureBoxSelector)
	{
		$this->pictureBoxSelector = $pictureBoxSelector;
		$this->selector			  = \VoidCore::getProperty ($pictureBoxSelector, 'Image');
		$this->clipboard		  = new WFClass ('System.Windows.Forms.Clipboard');
	}
	
	public function loadFromFile (string $file)
	{
        \VoidCore::setProperty ($this->pictureBoxSelector, 'Image', (new Image ())->loadFromFile ($file)->selector);
	}
	
	public function saveToFile (string $file)
	{
		\VoidCore::callMethod ($this->selector, 'Save', $file);
	}
	
	public function loadFromClipboard ()
	{
		\VoidCore::setProperty ($this->pictureBoxSelector, 'Image', $this->clipboard->getImage ());
	}
	
	public function saveToClipboard ()
	{
		$this->clipboard->setImage (\VoidCore::getProperty ($this->pictureBoxSelector, 'Image'));
	}
}
