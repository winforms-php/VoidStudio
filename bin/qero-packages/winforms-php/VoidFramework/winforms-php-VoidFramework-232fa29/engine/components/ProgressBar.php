<?php

namespace VoidEngine;

class ProgressBar extends Control
{
    public $class = 'System.Windows.Forms.ProgressBar';

    public function get_max ()
    {
        return $this->getProperty ('Maximum');
    }

    public function set_max (int $max)
    {
        $this->setProperty ('Maximum', $max);
    }

    public function get_min ()
    {
        return $this->getProperty ('Minimum');
    }

    public function set_min (int $min)
    {
        $this->setProperty ('Minimum', $min);
    }

    public function get_position ()
    {
        return $this->getProperty ('Value');
    }

    public function set_position (int $position)
    {
        $this->setProperty ('Value', $position);
    }
}
