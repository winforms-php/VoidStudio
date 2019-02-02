<?php

namespace VoidEngine;

abstract class Control extends Component
{
    public function __construct (Control $parent = null, string $className)
	{
        parent::__construct ($className);
        
		if ($parent)
			$this->set_parent ($parent);
	}
	
    public function get_name ()
    {
        return $this->getProperty ('Name');
    }
	
    public function set_name (string $name)
    {
        $this->setProperty ('Name', $name);
    }
	
    public function get_text ()
    {
        return $this->getProperty ('Text');
    }
	
    public function set_text (string $text)
    {
        $this->setProperty ('Text', $text);
    }
	
    public function get_caption ()
    {
        return $this->get_text ();
    }
	
    public function set_caption (string $caption)
    {
        $this->set_text ($caption);
    }

    public function get_font ()
	{
        $font = $this->getProperty ('Font');
        
		return [
            VoidEngine::getProperty ($font, 'Name'),
            VoidEngine::getProperty ($font, 'Size')
        ];
	}
	
	public function set_font (array $font)
	{
        $font = array_values ($font);

        $obj = new WFObject ('System.Drawing.Font', 'System.Drawing');
        $obj->token = 'b03f5f7f11d50a3a';

        $obj = VoidEngine::createObject ($obj, $font[0], [$font[1], 'float']);
        
		$this->setProperty ('Font', $obj);
    }
    
    public function get_flatStyle ()
    {
        return $this->getProperty ('FlatStyle');
    }
	
    public function set_flatStyle (int $style)
    {
        $this->setProperty ('FlatStyle', $style);
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
	
    public function get_parent ()
    {
        return $this->getProperty ('Parent');
    }
	
    public function set_parent ($parent)
    {
        if ($parent instanceof Control)
            $this->setProperty ('Parent', $parent->selector);

        elseif (is_numeric ($parent))
            $this->setProperty ('Parent', $parent);

        else throw new \Exception ('$parent must be instance of "Control" or his selector');
    }
	
    public function get_width ()
    {
        return $this->getProperty ('Width');
    }
	
    public function set_width (int $width)
    {
        $this->setProperty ('Width', $width);
    }
	
    public function get_w ()
    {
        return $this->get_width ();
    }
	
    public function set_w (int $w)
    {
        $this->set_width ($w);
    }
	
    public function get_height ()
    {
        return $this->getProperty ('Height');
    }
	
    public function set_height (int $height)
    {
        $this->setProperty ('Height', $height);
    }
	
    public function get_h ()
    {
        return $this->get_height ();
    }
	
    public function set_h (int $h)
    {
        $this->set_height ($h);
    }
	
    public function get_left ()
    {
        return $this->getProperty ('Left');
    }
	
    public function set_left (int $left)
    {
        $this->setProperty ('Left', $left);
    }
	
    public function get_x ()
    {
        return $this->get_left ();
    }
	
    public function set_x (int $x)
    {
        $this->set_left ($x);
    }
	
    public function get_top ()
    {
        return $this->getProperty ('Top');
    }
	
    public function set_top (int $top)
    {
        $this->setProperty ('Top', $top);
    }
	
    public function get_y ()
    {
        return $this->get_top ();
    }
	
    public function set_y (int $y)
    {
        $this->set_top ($y);
    }

    public function get_bounds ()
    {
        return [
            $this->get_left (),
            $this->get_top (),
            $this->get_width (),
            $this->get_height ()
        ];
    }
	
    public function set_bounds (array $bounds)
    {
        $bounds = array_values ($bounds);
        
        $this->set_left ($bounds[0]);
        $this->set_top ($bounds[1]);
        $this->set_width ($bounds[2]);
        $this->set_height ($bounds[3]);
    }
	
    public function get_location ()
    {
        return [
            $this->get_left (),
            $this->get_top ()
        ];
    }
	
    public function set_location (array $location)
    {
        $location = array_values ($location);
        
        $this->set_left ($location[0]);
        $this->set_top ($location[1]);
    }
	
    public function get_size ()
    {
        return [
            $this->get_width (),
            $this->get_height ()
        ];
    }
	
    public function set_size (array $size)
    {
        $size = array_values ($size);
        
        $this->set_width ($size[0]);
        $this->set_height ($size[1]);
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

    public function get_bottom ()
    {
        return $this->getProperty ('Bottom');
    }
	
    public function get_right ()
    {
        return $this->getProperty ('Right');
    }

    public function get_autoSize ()
    {
        return $this->getProperty ('AutoSize');
    }

    public function set_autoSize (bool $autoSize)
    {
        return $this->setProperty ('AutoSize', $autoSize);
    }

    public function get_dock ()
    {
        return $this->getProperty ('Dock');
    }
	
    public function set_dock (int $dock)
    {
        $this->setProperty ('Dock', $dock);
    }
	
    public function get_enabled ()
    {
        return $this->getProperty ('Enabled');
    }
	
    public function set_enabled (bool $enabled)
    {
        $this->setProperty ('Enabled', $enabled);
    }
	
    public function get_visible ()
    {
        return $this->getProperty ('Visible');
    }
	
    public function set_visible (bool $visible)
    {
        $this->setProperty ('Visible', $visible);
    }
    
    public function get_anchor ()
    {
        return $this->getProperty ('Anchor');
    }

    /*public function set_anchor (array $anchors)
    {
        $anchor = 0;

        foreach ($anchors as $id => $anc)
            $anchor |= $anc;

        return $this->setProperty ('Anchor', $anchor, 'int');
    }*/

    public function set_anchor (int $anchor)
    {
        $this->setProperty ('Anchor', $anchor);
    }

    public function get_borderStyle ()
	{
		return $this->getProperty ('BorderStyle');
	}
	
	public function set_borderStyle (int $borderStyle)
	{
		$this->setProperty ('BorderStyle', $borderStyle);
	}

    public function get_handle ()
    {
        return $this->getProperty ('Handle');
    }
	
    public function toBack()
    {
        $this->callMethod ('SendToBack');
    }
	
    public function toFront ()
    {
        $this->callMethod ('BringToFront');
    }
	
    public function show ()
    {
        $this->callMethod ('Show');
    }

    public function showDialog ()
    {
        $this->callMethod ('ShowDialog');
    }
	
    public function hide ()
    {
        $this->callMethod ('Hide');
    }

    public function close ()
	{
		$this->callMethod ('Close');
	}
	
    public function focus ()
    {
        return $this->callMethod ('Focus');
    }
	
	public function dispose ()
	{
        VoidEngine::removeObject ($this->getProperty ('Controls'));

		parent::dispose ();
	}
}

abstract class NoVisual extends Control {}

?>
