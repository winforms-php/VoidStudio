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
	
    public function get_image ()
    {
        return $this->image;
    }
	
    public function get_imageLocation ()
    {
        return $this->getProperty ('ImageLocation');
    }
	
    public function set_imageLocation (string $path)
    {
        $this->setProperty ('ImageLocation', $path);
    }

    public function get_sizeMode ()
    {
        return $this->getProperty ('SizeMode');
    }

    public function set_sizeMode (int $sizeMode)
    {
        $this->setProperty ('SizeMode', $sizeMode);
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
        $obj = new WFObject ('System.Drawing.Image', 'System.Drawing');
        $obj->token = 'b03f5f7f11d50a3a';

        $obj   = VoidEngine::buildObject ($obj);
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
            self::$clipboard = VoidEngine::buildObject (new WFObject ('System.Windows.Forms.Clipboard'));

        $image = VoidEngine::callMethod (self::$clipboard, 'GetImage');
        
        VoidEngine::getProperty ($this->pictureBoxSelector, 'Image', $image);
        
		if (!isset ($this->selector))
		    $this->selector = $image;
	}
	
	public function saveToClipboard ()
	{
		if (!isset (self::$clipboard))
            self::$clipboard = VoidEngine::buildObject (new WFObject ('System.Windows.Forms.Clipboard'));

        $image = VoidEngine::getProperty ($this->pictureBoxSelector, 'Image');
        
		VoidEngine::callMethod (self::$clipboard, 'SetImage', $image);
	}
}

?>
