<?php

namespace VoidEngine;

abstract class FileDialog extends CommonDialog
{
    public function __construct (string $className)
    {
        parent::__construct ($className);
    }
	
    public function get_filenames ()
    {
        return $this->getArrayProperty ('FileNames', 'string');
    }
}

?>
