<?php

namespace VoidEngine;

class SaveFileDialog extends FileDialog
{
	public function __construct ()
    {
        parent::__construct (self::class);
    }
	
    public function get_createPrompt ()
    {
        return $this->getProperty ('CreatePrompt');
    }
	
    public function set_createPrompt (bool $createPrompt)
    {
        $this->setProperty ('CreatePrompt', $createPrompt);
    }
}

?>
