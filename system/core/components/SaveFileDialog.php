<?php

namespace VoidEngine;

class SaveFileDialog extends FileDialog
{
	public function __construct ()
    {
        parent::__construct (__CLASS__);
    }
	
    public function get_createPrompt ()
    {
        return $this->getProperty ('CreatePrompt', 'bool');
    }
	
    public function set_createPrompt (bool $createPrompt)
    {
        $this->setProperty ('CreatePrompt', $createprompt, 'bool');
    }
}

?>
