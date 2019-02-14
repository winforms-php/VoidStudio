<?php

namespace VoidEngine;

class TreeView extends Control
{
    protected $nodes;

    public function __construct (Control $parent = null)
    {
        parent::__construct ($parent, self::class);

        $this->nodes = new Items ($this->getProperty ('Nodes'));
    }

    public function get_path ()
    {
        try
        {
            $node = $this->selectedNode;
        }

        catch (\Throwable $e)
        {
            return false;
        }
        
        return $node->fullPath;
    }
}

class TreeNode extends Control
{
    protected $nodes;

    public function __construct (string $text = '')
    {
        parent::__construct (null, self::class);

        $this->text  = $text;
        $this->nodes = new Items ($this->getProperty ('Nodes'));
    }

    public function get_path ()
    {
        return $this->getProperty ('FullPath');
    }
}

?>
