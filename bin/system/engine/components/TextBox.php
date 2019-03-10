<?php

namespace VoidEngine;

class TextBox extends Control
{
    public function __construct (Control $parent = null)
	{
        parent::__construct ($parent, self::class);
	}
}

class MaskedTextBox extends TextBox {}
class RichTextBox extends TextBox {}

?>
