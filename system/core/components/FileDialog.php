<?php

namespace VoidEngine;

abstract class FileDialog extends CommonDialog
{
    public function __construct (string $className)
    {
        parent::__construct ($className);
    }

    public function get_filename ()
    {
        return $this->getProperty ('FileName', 'string');
    }
	
    public function set_filename (string $filename)
    {
        $this->setProperty ('FileName', $filename, 'string');
    }
	
    public function get_filenames ()
    {
        return $this->getArrayProperty ('FileNames','string');
    }
	
    public function get_title ()
    {
        return $this->getProperty ('Title', 'string');
    }
	
    public function set_title (string $title)
    {
        $this->setProperty ('Title', $title, 'string');
    }
	
    public function get_filter ()
    {
        return $this->getProperty ('Filter', 'string');
    }
	
    public function set_filter (string $filter)
    {
        $this->setProperty ('Filter', $filter, 'string');
    }
	
    public function get_filterIndex ()
    {
        return $this->getProperty ('FilterIndex', 'int');
    }
	
    public function set_filterIndex (int $index)
    {
        $this->setProperty ('FilterIndex', $index, 'int');
    }
	
    public function get_defaultExt ()
    {
        return $this->getProperty ('DefaultExt', 'string');
    }
	
    public function set_defaultExt (string $ext)
    {
        $this->setProperty ('DefaultExt', $ext, 'string');
    }
	
    public function get_addExtension ()
    {
        return $this->getProperty ('AddExtension', 'bool');
    }
	
    public function set_addExtension (bool $add)
    {
        $this->setProperty ('AddExtension', $add, 'bool');
    }
	
    public function get_checkPathExists ()
    {
        return $this->getProperty ('CheckPathExists', 'bool');
    }
	
    public function set_checkPathExists (bool $checkpath)
    {
        $this->setProperty ('CheckPathExists', $checkpath, 'bool');
    }
}

?>
