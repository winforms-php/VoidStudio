<?php

namespace VoidEngine;

class WebBrowser extends Control
{
    public $class = 'System.Windows.Forms.WebBrowser';

    public function back (): void
    {
        $this->callMethod ('GoBack');
    }

    public function forward (): void
    {
        $this->callMethod ('GoForward');
    }

    public function browse (string $url)
    {
        return $this->callMethod ('Navigate', $url);
    }
}
