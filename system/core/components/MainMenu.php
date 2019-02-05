<?php

namespace VoidEngine;

class MainMenu extends Component
{
    protected $items;

    public function __construct ()
    {
        parent::__construct (self::class);

        $this->items = new MenuItems ($this->getProperty ('MenuItems'));
    }

    public function get_items ()
    {
        return $this->items;
    }
}

class ContextMenu extends Component
{
    public $items;

    public function __construct ()
    {
        parent::__construct (self::class);

        $this->items = new MenuItems ($this->getProperty ('MenuItems'));
    }

    public function get_items ()
    {
        return $this->items;
    }
}

class MenuItem extends Control
{
    public $items;

    public function __construct (string $text = '')
    {
        parent::__construct (null, self::class);

        $this->text  = $text;
        $this->items = new MenuItems ($this->getProperty ('MenuItems'));
    }

    public function get_items ()
    {
        return $this->items;
    }

    public function get_index ()
    {
        return $this->getProperty ('Index');
    }
}

class MenuItems extends Items
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
		return $this->offsetSet (null, $value instanceof MenuItem ? $value->selector : $value);
	}
	
	public function append ($value)
	{
		return $this->offsetSet (null, $value instanceof MenuItem ? $value->selector : $value);
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
