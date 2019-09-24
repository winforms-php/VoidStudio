<?php

namespace VoidEngine;

class TreeView extends Control
{
    public $class = 'System.Windows.Forms.TreeView';

    public function get_path ()
    {
        try
        {
            $node = $this->selectedNode;
        }

        catch (\WinFormsException $e)
        {
            return false;
        }
        
        return $node->fullPath;
    }
}

class TreeNode extends Control
{
    public $class = 'System.Windows.Forms.TreeNode';

    public function __construct (string $text = '')
    {
        parent::__construct (null, $this->class);

        $this->text = $text;
    }

    public function get_path ()
    {
        return $this->getProperty ('FullPath');
    }
}
