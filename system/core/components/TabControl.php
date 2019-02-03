<?php

namespace VoidEngine;

class TabControl extends Control
{
    protected $items;

    public function __construct (Control $parent = null)
	{
        parent::__construct ($parent, self::class);

        $this->items = new TabPages ($this->getProperty ('TabPages'));
    }
    
    public function get_items ()
    {
        return $this->items;
    }

    public function get_selectedTab ()
    {
        return $this->getProperty ('SelectedTab');
    }

    public function set_selectedTab (int $selector)
    {
        $this->setProperty ('SelectedTab', $selector);
    }

    public function get_selectedIndex ()
    {
        return $this->getProperty ('SelectedIndex');
    }

    public function set_selectedIndex (int $index)
    {
        $this->setProperty ('SelectedIndex', $index);
    }
}

class TabPage extends Control
{
    public function __construct (string $text = '')
    {
        parent::__construct (null, self::class);

        $this->text = $text;
    }
}

class TabPages extends Items
{
    public function __get ($name)
	{
		switch (strtolower ($name))
		{
			case 'count':
                return VoidEngine::getProperty ($this->selector, 'Count');
            break;
				
            case 'list':
                $size = VoidEngine::getProperty ($this->selector, 'Count');
                $list = [];
                
				for ($i = 0; $i < $size; ++$i)
                    $list[] = VoidEngine::getArrayValue ($this->selector, $i);
                    
                return $list;
            break;

            case 'names':
                $size = VoidEngine::getProperty ($this->selector, 'Count');
                $names = [];
                
                for ($i = 0; $i < $size; ++$i)
                    $names[] = VoidEngine::getProperty (VoidEngine::getArrayValue ($this->selector, [$i, 'object']), 'Text');
                
                return $names;
            break;
		}
	}
	
	public function add ($value)
	{
        return $this->offsetSet (null, $value instanceof TabPage ? $value->selector : $value);
	}
	
	public function append ($value)
	{
        return $this->offsetSet (null, $value instanceof TabPage ? $value->selector : $value);
	}
	
	public function offsetSet ($index, $value)
	{
        return $index === null ?
            VoidEngine::callMethod ($this->selector, 'Add', $value) :
            VoidEngine::callMethod ($this->selector, 'Insert', $index, $value);
	}
	
	public function offsetGet ($index)
	{
		return VoidEngine::getArrayValue ($this->selector, $index);
	}
}

?>
