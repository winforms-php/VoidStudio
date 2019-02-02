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
        return $this->getProperty ('FileName');
    }
	
    public function set_filename (string $filename)
    {
        $this->setProperty ('FileName', $filename);
    }
	
    public function get_filenames ()
    {
        return $this->getArrayProperty ('FileNames', 'string');
    }
	
    public function get_title ()
    {
        return $this->getProperty ('Title');
    }
	
    public function set_title (string $title)
    {
        $this->setProperty ('Title', $title);
    }
	
    public function get_filter ()
    {
        return $this->getProperty ('Filter');
    }
	
    public function set_filter (string $filter)
    {
        $this->setProperty ('Filter', $filter);
    }
	
    public function get_filterIndex ()
    {
        return $this->getProperty ('FilterIndex');
    }
	
    public function set_filterIndex (int $index)
    {
        $this->setProperty ('FilterIndex', $index);
    }
	
    public function get_defaultExt ()
    {
        return $this->getProperty ('DefaultExt');
    }
	
    public function set_defaultExt (string $ext)
    {
        $this->setProperty ('DefaultExt', $ext);
    }
	
    public function get_addExtension ()
    {
        return $this->getProperty ('AddExtension');
    }
	
    public function set_addExtension (bool $add)
    {
        $this->setProperty ('AddExtension', $add);
    }
	
    public function get_checkPathExists ()
    {
        return $this->getProperty ('CheckPathExists');
    }
	
    public function set_checkPathExists (bool $checkPath)
    {
        $this->setProperty ('CheckPathExists', $checkPath);
    }
}

?>
