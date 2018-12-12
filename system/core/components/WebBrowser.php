<?php

namespace VoidEngine;

class WebBrowser extends Control
{
    public function __construct (Control $parent = null)
    {
        parent::__construct ($parent, self::class);
    }

    public function get_scriptErrorsSuppressed ()
    {
        return $this->getProperty ('ScriptErrorsSuppressed', 'bool');
    }
	
    public function set_scriptErrorsSuppressed (bool $supress)
    {
        $this->setProperty ('ScriptErrorsSuppressed', $supress, 'bool');
    }
	
    public function browse (string $url)
    {
        return $this->callMethod ('Navigate', '', $url, 'string');
    }
	
    public function loadHTML (string $html)
    {
        return $this->callMethod ('LoadHTML', '', $html, 'string');
    }
}

?>
