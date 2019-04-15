<?php

namespace VoidEngine;

class WebBrowser extends Control
{
    public $class = 'System.Windows.Forms.WebBrowser';
	
    public function browse (string $url)
    {
        return $this->callMethod ('Navigate', $url);
    }
	
    public function loadHTML (string $html)
    {
        return $this->callMethod ('LoadHTML', $html);
    }
}
