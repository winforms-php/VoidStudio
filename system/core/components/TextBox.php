<?php

namespace VoidEngine;

class TextBox extends Control
{
    public function __construct (Control $parent = null)
	{
        parent::__construct ($parent, self::class);
	}
	
    public function get_passwordChar ()
    {
        return $this->getProperty (['PasswordChar', 'string']);
    }
	
    public function set_passwordChar (string $char)
    {
        $this->setProperty ('PasswordChar', [$char, 'char']);
    }
	
    public function get_multiline ()
    {
        return $this->getProperty (['Multiline', 'bool']);
    }
	
    public function set_multiline (bool $multiline)
    {
        $this->setProperty ('Multiline', [$multiline, 'bool']);
    }
	
    public function get_readOnly ()
    {
        return $this->getProperty (['ReadOnly', 'bool']);
    }
	
    public function set_readOnly (bool $readOnly)
    {
        $this->setProperty ('ReadOnly', [$readOnly, 'bool']);
    }

    public function get_wordWrap ()
    {
        return $this->getProperty (['WordWrap', 'bool']);
    }

    public function set_wordWrap (bool $wordWrap)
    {
        return $this->setProperty ('WordWrap', [$wordWrap, 'bool']);
    }
}

?>
