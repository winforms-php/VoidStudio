<?php

namespace VoidEngine;

class PictureBox extends Control
{
	protected $image;
	
	public function __construct (Control $parent = null)
	{
        parent::__construct ($parent, self::class);
        
		$this->image = new PictureBoxImage ($this->componentSelector);
	}
}

class PictureBoxImage
{
    protected $selector;
    protected $pictureBoxSelector;

    static $clipboard;
    
	public function __construct (int $pictureBoxSelector)
	{
		$this->pictureBoxSelector = $pictureBoxSelector;
	}
	
	public function loadFromFile (string $file)
	{
        $obj = new ObjectType ('System.Drawing.Image');
        $obj->token = 'b03f5f7f11d50a3a';

        $obj   = VoidEngine::createClass ($obj);
        $image = VoidEngine::callMethod ($obj, 'FromFile', $file);
        
        VoidEngine::setProperty ($this->pictureBoxSelector, 'Image', $image);
        
		if (!isset ($this->selector))
            $this->selector = VoidEngine::getProperty ($this->pictureBoxSelector, 'Image');
        
		VoidEngine::removeObject ($obj, $image);
	}
	
	public function saveToFile (string $file)
	{
		if ($this->selector)
			VoidEngine::callMethod ($this->selector, 'Save', $file);
	}
	
	public function loadFromClipboard ()
	{
		if (!isset (self::$clipboard))
            self::$clipboard = new WFClass ('System.Windows.Forms.Clipboard');

        $image = self::$clipboard->getImage ();
        
        VoidEngine::getProperty ($this->pictureBoxSelector, 'Image', $image);
        
		if (!isset ($this->selector))
		    $this->selector = $image;
	}
	
	public function saveToClipboard ()
	{
		if (!isset (self::$clipboard))
            self::$clipboard = new WFClass ('System.Windows.Forms.Clipboard');

        $image = VoidEngine::getProperty ($this->pictureBoxSelector, 'Image');
        
		self::$clipboard->setImage ($image);
	}
}

?>
