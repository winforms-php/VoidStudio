<?php

namespace VoidEngine;

class VoidDesigner extends Component
{
    protected $form;
    protected $control;
    protected $host;
    protected $selService;

    public function __construct (Form $form, PropertyGrid $propertyGrid)
    {
        $this->componentSelector = VoidEngine::createObject (new WFObject ('WinForms_PHP.FormDesigner', false, true));
        Components::addComponent ($this->componentSelector, $this);

        $this->form       = $this->callMethod ('GetForm', 'object');
        $this->control    = $this->callMethod ('GetControl', 'object');
        $this->host       = $this->callMethod ('GetHost', 'object');
        $this->selService = $this->callMethod ('GetSelService', 'object');

        VoidEngine::setProperty ($this->form, 'Text', 'Form Caption', 'string');
        VoidEngine::setProperty ($this->control, 'Parent', $form->selector, 'object');

        VoidEngine::setObjectEvent ($this->selService, 'SelectionChanged', '
            $objects = VoidEngine\VoidEngine::callMethod ('. $this->componentSelector .', "GetSelectedComponents", "object");

            VoidEngine\VoidEngine::setProperty ('. $propertyGrid->selector .', "SelectedObjects", $objects, "object");

            $firstObject = VoidEngine\VoidEngine::getArrayValue ($objects, 0, "object");
            $content     = VoidEngine\VoidEngine::callMethod ($firstObject, "ToString");
            $className   = substr (explode (".", explode (",", $content)[0])[3], 0, -1);

            VoidEngine\VoidStudioAPI::getObjects ("main")["Objects"]->selectedItem = "[$firstObject] $className";
        ');
    }

    public function updateHost ()
    {
        $this->callMethod ('UpdateHost');
    }

    public function addComponent (Control $component, string $name)
    {
        VoidEngine::callMethod ($this->host, 'Add', '', $component->selector, 'object', $name, 'string');
        VoidEngine::setProperty ($component->selector, 'Parent', $this->form, 'object');
    }
}

?>
