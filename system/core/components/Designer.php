<?php

namespace VoidEngine;

class VoidDesigner extends Component
{
    protected $form;
    protected $control;
    protected $host;

    public function __construct (Form $form, PropertyGrid $propertyGrid)
    {
        $this->componentSelector = VoidEngine::createObject (new WFObject ('WinForms_PHP.FormDesigner', false, true), $propertyGrid->selector, 'object');
        Components::addComponent ($this->componentSelector, $this);

        $this->form    = $this->callMethod ('GetForm', 'object');
        $this->control = $this->callMethod ('GetControl', 'object');
        $this->host    = $this->callMethod ('GetHost', 'object');

        VoidEngine::setProperty ($this->form, 'Text', 'Form Caption', 'string');
        VoidEngine::setProperty ($this->control, 'Parent', $form->selector, 'object');
    }

    public function addComponent (Control $component, string $name)
    {
        VoidEngine::callMethod ($this->host, 'Add', '', $component->selector, 'object', $name, 'string');
        VoidEngine::setProperty ($component->selector, 'Parent', $this->form, 'object');
    }
}

?>
