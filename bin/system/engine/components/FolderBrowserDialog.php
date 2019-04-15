<?php

namespace VoidEngine;

class FolderBrowserDialog extends CommonDialog
{
    public $class = 'System.Windows.Forms.FolderBrowserDialog';
	
    public function get_path ()
    {
        return $this->getProperty ('SelectedPath');
    }
}
