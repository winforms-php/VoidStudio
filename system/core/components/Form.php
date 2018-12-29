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
		return Components::getComponent ($this->getProperty ('Owner', 'object'));
	}
	
	public function set_owner (Form $form)
	{
		$this->setProperty ('Owner', $form->selector, 'object');
	}

	public function get_icon ()
	{
		return new FormIcon ($this->componentSelector);
	}
	
	public function get_opacity ()
	{
		return $this->getProperty ('Opacity', 'double');
	}
	
	public function set_opacity ($opacity) // float
	{
		$this->setProperty ('Opacity', (float) $opacity, 'double');
	}

	public function get_borderStyle ()
	{
		return $this->getProperty ('FormBorderStyle', 'int');
	}
	
	public function set_borderStyle (int $borderStyle)
	{
		$this->setProperty ('FormBorderStyle', $borderStyle, 'int');
	}
	
	public function get_windowState ()
	{
		return $this->getProperty ('WindowState', 'int');
	}
	
	public function set_windowState (int $windowState)
	{
		$this->setProperty ('WindowState', $windowState, 'int');
	}
	
	public function get_startPosition ()
	{
		return $this->getProperty ('StartPosition', 'int');
	}
	
	public function set_startPosition (int $startPosition)
	{
		$this->setProperty ('StartPosition', $startPosition, 'int');
	}
	
	public function get_showInTaskbar ()
	{
		return $this->getProperty ('ShowInTaskbar', 'bool');
	}
	
	public function set_showInTaskbar (bool $showInTaskbar)
	{
		$this->setProperty ('ShowInTaskbar', $showInTaskbar, 'bool');
	}
	
	public function get_minimizeBox ()
	{
		return $this->getProperty ('MinimizeBox', 'bool');
	}
	
	public function set_minimizeBox (bool $minimizeBox)
	{
		$this->setProperty ('MinimizeBox', $minimizeBox, 'bool');
	}
	
	public function get_maximizeBox ()
	{
		return $this->getProperty ('MaximizeBox', 'bool');
	}
	
	public function set_maximizeBox (bool $maximizeBox)
	{
		$this->setProperty ('MaximizeBox', $maximizebox, 'bool');
	}
	
	public function get_controlBox ()
	{
		return $this->getProperty ('ControlBox', 'bool');
	}
	
	public function set_controlBox (bool $controlBox)
	{
		$this->setProperty ('ControlBox', $controlBox, 'bool');
	}
	
	public function get_showIcon ()
	{
		return $this->getProperty ('ShowIcon', 'bool');
	}
	
	public function set_showIcon (bool $showIcon)
	{
		$this->setProperty ('ShowIcon', $showIcon, 'bool');
	}
	
	public function get_topLevel ()
	{
		return $this->getProperty ('TopLevel', 'bool');
	}
	
	public function set_topLevel (bool $level)
	{
		$this->setProperty ('TopLevel', $level, 'bool');
	}
	
	public function get_acceptButton ()
	{
		return Components::getComponent ($this->getProperty ('AcceptButton', 'object'));
	}
	
	public function set_acceptButton (Button $button)
	{
		$this->setProperty ('AcceptButton', $button->selector, 'object');
	}
	
	public function get_cancelButton ()
	{
		return Components::getComponent ($this->getProperty ('CancelButton', 'object'));
	}
	
	public function set_cancelButton (Button $button)
	{
		$this->setProperty ('CancelButton', $button->selector, 'object');
	}
	
	public function get_clientSize ()
	{
		$obj = $this->getProperty ('ClientSize', 'object');

		$w = VoidEngine::getProperty ($obj, 'Width', 'int');
		$h = VoidEngine::getProperty ($obj, 'Height', 'int');
		
		VoidEngine::removeObject ($obj);
		
		return [$w, $h];
	}
	
	public function set_clientSize (array $size)
	{
		$obj = $this->getProperty ('ClientSize', 'object');

		VoidEngine::setProperty ($obj, 'Width', array_shift ($size), 'int');
		VoidEngine::setProperty ($obj, 'Height', array_shift ($size), 'int');

		$this->setProperty ('ClientSize', $obj, 'object');

		VoidEngine::removeObject ($obj);
	}
	
	public function dispose ()
	{
		VoidEngine::removeObject ($this->getProperty ('Icon', 'object'));

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

		$icon = VoidEngine::createObject ($icon, text ($file), 'string');
        
        VoidEngine::setProperty ($this->formSelector, 'Icon', $icon, 'object');

		if (!isset ($this->selector))
		    $this->selector = $icon;
	}
}

?>
