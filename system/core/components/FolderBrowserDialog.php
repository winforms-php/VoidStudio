<?php

namespace VoidEngine;

class FolderBrowserDialog extends CommonDialog
{
    public function __construct ()
    {
        parent::__construct (__CLASS__);
    }
	
    public function get_path ()
    {
        return $this->getProperty ('SelectedPath', 'string');
    }
}

?>
