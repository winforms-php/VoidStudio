<?php

namespace VoidEngine;

class TreeView extends Control
{
    protected $nodes;

    public function __construct (Control $parent = null)
    {
        parent::__construct ($parent, self::class);

        $this->nodes = new TreeViewNodes ($this->getProperty ('Nodes'));
    }

    public function get_nodes ()
    {
        return $this->nodes;
    }

    public function get_selectedNode ()
    {
        return $this->getProperty ('SelectedNode');
    }

    public function get_path ()
    {
        try
        {
            $node = $this->get_selectedNode ();
        }

        catch (\Throwable $e)
        {
            return false;
        }
        
        return VoidEngine::getProperty ($node, 'FullPath');
    }

    public function dispose ()
	{
        VoidEngine::removeObject ($this->getProperty ('Nodes'));
        unset ($this->nodes);
        
        parent::dispose ();
	}
}

class TreeNode extends Control
{
    protected $nodes;

    public function __construct (string $text = '')
    {
        parent::__construct (null, self::class);

        $this->text  = $text;
        $this->nodes = new TreeViewNodes ($this->getProperty ('Nodes'));
    }

    public function get_nodes ()
    {
        return $this->nodes;
    }

    public function get_path ()
    {
        return $this->getProperty ('FullPath');
    }
}

class TreeViewNodes extends Items
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
        $this->offsetSet (null, $value instanceof TreeNode ? $value->selector : $value);
        
        if ($value instanceof TreeNode)
            return $value->nodes;
	}
	
	public function append ($value)
	{
        $this->offsetSet (null, $value instanceof TreeNode ? $value->selector : $value);
        
        if ($value instanceof TreeNode)
            return $value->nodes;
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
