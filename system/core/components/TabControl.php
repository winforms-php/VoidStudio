<?php

namespace VoidEngine;

class TabControl extends Control
{
    protected $tabs;

    public function __construct (Control $parent = null)
	{
        parent::__construct ($parent, self::class);

        $this->tabs = new TabPages ($this->getProperty ('TabPages', 'object'));
    }
    
    public function get_tabPages ()
    {
        return $this->tabs;
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
                return VoidEngine::getProperty ($this->selector, 'Count', 'int');
            break;
				
            case 'list':
                $size = VoidEngine::getProperty ($this->selector, 'Count', 'int');
                $list = [];
                
				for ($i = 0; $i < $size; ++$i)
                    $list[] = VoidEngine::getArrayValue ($this->selector, $i, 'object');
                    
                return $list;
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
            VoidEngine::callMethod ($this->selector, 'Add', '', $value, 'object') :
            VoidEngine::callMethod ($this->selector, 'Insert', '', (int) $index, 'int', $value, 'object');
	}
	
	public function offsetGet ($index)
	{
		return VoidEngine::getArrayValue ($this->selector, (int) $index, 'object');
	}
}

?>
