<?php

namespace VoidEngine;

class ColorDialog extends CommonDialog
{
    public function __construct ()
    {
        parent::__construct (self::class);
    }
	
    public function get_color ()
    {
        return $this->getProperty (['Color', 'color']);
    }
	
    public function set_color (int $color)
    {
        $this->setProperty ('Color', [$color, 'color']);
    }
}

?>
