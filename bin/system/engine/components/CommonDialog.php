<?php

namespace VoidEngine;

abstract class CommonDialog extends Component
{
    public $class = 'System.Windows.Forms.CommonDialog';

    public function execute (): int
    {
        return $this->callMethod ('ShowDialog');
    }
}
