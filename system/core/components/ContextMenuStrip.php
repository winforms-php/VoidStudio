<?php

namespace VoidEngine;

class ContextMenuStrip extends NoVisual
{
    protected $items;

    public function __construct ()
    {
        parent::__construct (null, self::class);

        $this->items = $this->getProperty ('Items', 'object');
    }

    public function get_items ()
    {
        return $this->items;
    }
}

?>
