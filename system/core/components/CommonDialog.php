<?php

namespace VoidEngine;

abstract class CommonDialog extends Component
{
    public function __construct (string $className)
    {
        parent::__construct ($className);
    }
    
    public function showDialog ()
    {
        return (int) $this->callMethod ('ShowDialog', 'int');
    }

    public function execute ()
    {
        return $this->showDialog ();
    }
}

?>
