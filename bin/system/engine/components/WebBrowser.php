<?php

namespace VoidEngine;

class WebBrowser extends Control
{
    public function __construct (Control $parent = null)
    {
        parent::__construct ($parent, self::class);
    }
	
    public function browse (string $url)
    {
        return $this->callMethod ('Navigate', $url);
    }
}

?>
