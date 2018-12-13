<?php

namespace VoidEngine;

class MessageBox extends Component
{
    public function __construct ($text = null)
    {
        $this->componentSelector = VoidEngine::buildObject (new WFObject ('System.Windows.Forms.MessageBox'));
        $this->componentClass    = 'MessageBox';

        if ($text !== null)
            $this->show ($text);
    }

    public function show ($text)
    {
        $this->callMethod ('Show', '', $text, 'string');
    }
}

?>
