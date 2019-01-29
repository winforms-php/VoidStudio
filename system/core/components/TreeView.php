<?php

namespace VoidEngine;

class TreeView extends Control
{
    protected $nodes;

    public function __construct (Control $parent = null)
    {
        parent::__construct ($parent, self::class);

        $this->nodes = new TreeViewNodes ($this->getProperty ('Nodes', 'object'));
    }

    public function get_nodes ()
    {
        return $this->nodes;
    }

    public function get_selectedNode ()
    {
        return $this->getProperty ('SelectedNode', 'object');
    }

    public function get_path ()
    {
        try
        {
            $node = $this->get_selectedNode ();
        }

        catch (\Exception $error)
        {
            return false;
        }
        
        return VoidEngine::getProperty ($node, 'FullPath', 'string');
    }

    public function dispose ()
	{
        VoidEngine::removeObject ($this->getProperty ('Nodes', 'object'));
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
        $this->nodes = new TreeViewNodes ($this->getProperty (['Nodes', 'object']));
    }

    public function get_nodes ()
    {
        return $this->nodes;
    }

    public function get_path ()
    {
        return $this->getProperty (['FullPath', 'string']);
    }
}

class TreeViewNodes extends Items
{
    public function __get ($name)
	{
		switch (strtolower ($name))
		{
			case 'count':
                return VoidEngine::getProperty ($this->selector, ['Count', 'int']);
            break;
				
            case 'list':
                $size = VoidEngine::getProperty ($this->selector, ['Count', 'int']);
                $list = [];
                
				for ($i = 0; $i < $size; ++$i)
                    $list[] = VoidEngine::getArrayValue ($this->selector, [$i, 'object']);
                    
                return $list;
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
            VoidEngine::callMethod ($this->selector, 'Add', [$value, 'object']) :
            VoidEngine::callMethod ($this->selector, 'Insert', [(int) $index, 'int'], [$value, 'object']);
	}
	
	public function offsetGet ($index)
	{
		return VoidEngine::getArrayValue ($this->selector, [(int) $index, 'object']);
	}
}

?>
