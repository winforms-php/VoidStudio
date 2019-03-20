<?php

namespace VoidEngine;

class ImageList extends Component
{
    protected $images;

    public function __construct ()
    {
        parent::__construct (self::class);

        $this->images = new Items ($this->getProperty ('Images'));
    }
}

?>