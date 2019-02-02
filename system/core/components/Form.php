<?php

namespace VoidEngine;

class Form extends Control
{
	public function __construct (Control $parent = null)
	{
        parent::__construct (null, self::class);
        
		if ($parent instanceof Control)
		{
			$this->set_topLevel (false);
			$this->set_parent ($parent);
		}
	}
	
	public function get_owner ()
	{
		return Components::getComponent ($this->getProperty ('Owner'));
	}
	
	public function set_owner (Form $form)
	{
		$this->setProperty ('Owner', $form->selector);
	}

	public function get_menu ()
	{
		return $this->getProperty ('Menu');
	}

	public function set_menu (MainMenu $menu)
	{
		$this->setProperty ('Menu', $menu->selector);
	}

	public function get_icon ()
	{
		return new FormIcon ($this->componentSelector);
	}
	
	public function get_opacity ()
	{
		return $this->getProperty ('Opacity');
	}
	
	public function set_opacity ($opacity) // float
	{
		$this->setProperty ('Opacity', (float) $opacity);
	}

	public function get_borderStyle ()
	{
		return $this->getProperty ('FormBorderStyle');
	}
	
	public function set_borderStyle (int $borderStyle)
	{
		$this->setProperty ('FormBorderStyle', $borderStyle);
	}
	
	public function get_windowState ()
	{
		return $this->getProperty ('WindowState');
	}
	
	public function set_windowState (int $windowState)
	{
		$this->setProperty ('WindowState', $windowState);
	}
	
	public function get_startPosition ()
	{
		return $this->getProperty ('StartPosition');
	}
	
	public function set_startPosition (int $startPosition)
	{
		$this->setProperty ('StartPosition', $startPosition);
	}
	
	public function get_showInTaskbar ()
	{
		return $this->getProperty ('ShowInTaskbar');
	}
	
	public function set_showInTaskbar (bool $showInTaskbar)
	{
		$this->setProperty ('ShowInTaskbar', $showInTaskbar);
	}
	
	public function get_minimizeBox ()
	{
		return $this->getProperty ('MinimizeBox');
	}
	
	public function set_minimizeBox (bool $minimizeBox)
	{
		$this->setProperty ('MinimizeBox', $minimizeBox);
	}
	
	public function get_maximizeBox ()
	{
		return $this->getProperty ('MaximizeBox');
	}
	
	public function set_maximizeBox (bool $maximizeBox)
	{
		$this->setProperty ('MaximizeBox', $maximizebox);
	}
	
	public function get_controlBox ()
	{
		return $this->getProperty ('ControlBox');
	}
	
	public function set_controlBox (bool $controlBox)
	{
		$this->setProperty ('ControlBox', $controlBox);
	}
	
	public function get_showIcon ()
	{
		return $this->getProperty ('ShowIcon');
	}
	
	public function set_showIcon (bool $showIcon)
	{
		$this->setProperty ('ShowIcon', $showIcon);
	}
	
	public function get_topLevel ()
	{
		return $this->getProperty ('TopLevel');
	}
	
	public function set_topLevel (bool $level)
	{
		$this->setProperty ('TopLevel', $level);
	}
	
	public function get_acceptButton ()
	{
		return Components::getComponent ($this->getProperty ('AcceptButton'));
	}
	
	public function set_acceptButton (Button $button)
	{
		$this->setProperty ('AcceptButton', $button->selector);
	}
	
	public function get_cancelButton ()
	{
		return Components::getComponent ($this->getProperty ('CancelButton'));
	}
	
	public function set_cancelButton (Button $button)
	{
		$this->setProperty ('CancelButton', $button->selector);
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
	
	public function dispose ()
	{
		VoidEngine::removeObject ($this->getProperty ('Icon'));

		parent::dispose ();
    }
}

class PseudoForm extends Form
{
    public function __construct (Control $parent = null)
	{
        $this->componentSelector = VoidEngine::createObject (new WFObject ('WinForms_PHP.PseudoForm', false, true));
		$this->componentClass    = self::class;
        
        Components::addComponent ($this->componentSelector, $this);
        
		if ($parent instanceof Control)
		{
			$this->set_topLevel (false);
			$this->set_parent ($parent);
		}
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
        $icon = new WFObject ('System.Drawing.Icon', 'System.Drawing');
        $icon->token = 'b03f5f7f11d50a3a';

		$icon = VoidEngine::createObject ($icon, text ($file));
        
        VoidEngine::setProperty ($this->formSelector, 'Icon', $icon);

		if (!isset ($this->selector))
		    $this->selector = $icon;
	}
}

?>
