<?php

namespace VoidEngine;

class Control extends Component
{
    public $class = 'System.Windows.Forms.Control';

    public function __construct (Component $parent = null, $className = null)
    {
        parent::__construct ($className);

        if ($parent)
            $this->parent = $parent;
    }
	
    public function get_caption (): string
    {
        return $this->text;
    }
	
    public function set_caption (string $caption)
    {
        $this->text = $caption;
    }

    public function get_font (): array
	{
        $font = $this->getProperty ('Font');
        
		return [
            VoidEngine::getProperty ($font, 'Name'),
            VoidEngine::getProperty ($font, 'Size')
        ];
	}
	
	public function set_font (array $font): void
	{
        $font = array_values ($font);

        $obj = isset ($font[2]) ?
            VoidEngine::createObject ('System.Drawing.Font', 'System.Drawing', $font[0], [$font[1], 'float'], [$font[2], 'System.Drawing.FontStyle, System.Drawing']) :
            VoidEngine::createObject ('System.Drawing.Font', 'System.Drawing', $font[0], [$font[1], 'float']);
        
		$this->setProperty ('Font', $obj);
    }
	
    public function get_backgroundColor ()
    {
        return $this->getProperty (['BackColor', 'color']);
    }
	
    public function set_backgroundColor ($color)
    {
        $this->setProperty ('BackColor', [$color, 'color']);
    }
	
    public function get_foregroundColor ()
    {
        return $this->getProperty (['ForeColor', 'color']);
    }
	
    public function set_foregroundColor ($color)
    {
        $this->setProperty ('ForeColor', [$color, 'color']);
    }
	
    public function get_w (): int
    {
        return $this->width;
    }
	
    public function set_w (int $w)
    {
        $this->width = $w;
    }
	
    public function get_h (): int
    {
        return $this->height;
    }
	
    public function set_h (int $h)
    {
        $this->height = $h;
    }
	
    public function get_x (): int
    {
        return $this->left;
    }
	
    public function set_x (int $x)
    {
        $this->left = $x;
    }
	
    public function get_y (): int
    {
        return $this->top;
    }
	
    public function set_y (int $y)
    {
        $this->top = $y;
    }

    public function get_bounds (): array
    {
        return [
            $this->left,
            $this->top,
            $this->width,
            $this->height
        ];
    }
	
    public function set_bounds (array $bounds)
    {
        $bounds = array_values ($bounds);
        
        $this->left   = (int) $bounds[0];
        $this->top    = (int) $bounds[1];
        $this->width  = (int) $bounds[2];
        $this->height = (int) $bounds[3];
    }
	
    public function get_location (): array
    {
        return [
            $this->left,
            $this->top
        ];
    }
	
    public function set_location (array $location)
    {
        $location = array_values ($location);
        
        $this->left = $location[0];
        $this->top  = $location[1];
    }
	
    public function get_size (): array
    {
        return [
            $this->width,
            $this->height
        ];
    }
	
    public function set_size (array $size)
    {
        $size = array_values ($size);
        
        $this->width  = (int) $size[0];
        $this->height = (int) $size[1];
    }
	
    public function setBounds (int $x, int $y, int $w, int $h)
    {
        $this->set_bounds ([$x, $y, $w, $h]);
    }
	
    public function setLocation (int $x, int $y)
    {
        $this->set_location ([$x, $y]);
    }
	
    public function setSize (int $w, int $h)
    {
        $this->set_size ([$w, $h]);
    }

    /*public function set_anchor (array $anchors)
    {
        $anchor = 0;

        foreach ($anchors as $anc)
            $anchor |= $anc;

        return $this->setProperty ('Anchor', $anchor, 'int');
    }*/
	
    public function toBack ()
    {
        $this->callMethod ('SendToBack');
    }
	
    public function toFront ()
    {
        $this->callMethod ('BringToFront');
    }
}

abstract class NoVisual extends Control {}
