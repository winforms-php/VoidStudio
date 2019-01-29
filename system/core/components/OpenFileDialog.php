<?php

namespace VoidEngine;

class OpenFileDialog extends FileDialog
{
    public function __construct ()
    {
        parent::__construct (self::class);
    }
	
    public function get_multiselect ()
    {
        return $this->getProperty (['Multiselect', 'bool']);
    }
	
    public function set_multiselect (bool $multiselect)
    {
        $this->setProperty ('Multiselect', [$multiselect, 'bool']);
    }
	
    public function get_checkFileExists ()
    {
        return $this->getProperty (['CheckFileExists', 'bool']);
    }
	
    public function set_checkFileExists (bool $checkfile)
    {
        $this->setProperty ('CheckFileExists', [$checkfile, 'bool']);
    }
	
    public function get_readOnlyChecked ()
    {
        return $this->getProperty (['ReadOnlyChecked', 'bool']);
    }
	
    public function set_readOnlyChecked (bool $checked)
    {
        $this->setProperty ('ReadOnlyChecked', [$checked, 'bool']);
    }
}

?>
