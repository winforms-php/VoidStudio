<?php

namespace VoidEngine;

abstract class CommonDialog extends Component
{
    public function __construct (string $className)
    {
        parent::__construct ($className);
    }
    
    public function showDialog (): int
    {
        return $this->callMethod ('ShowDialog');
    }

    public function execute (): int
    {
        return $this->showDialog ();
    }
}

?>
