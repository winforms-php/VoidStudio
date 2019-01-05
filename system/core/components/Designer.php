<?php

namespace VoidEngine;

class VoidDesigner extends Component
{
    protected $form;
    protected $control;
    protected $host;
    protected $selService;

    public function __construct (Control $parent, PropertyGrid $propertyGrid, string $formName = 'form')
    {
        $this->form = new Form;
        $this->form->caption = 'Form Caption';

        $this->componentSelector = VoidEngine::createObject (new WFObject ('WinForms_PHP.FormDesigner', false, true), $this->form->selector, 'object', $formName, 'string');
        Components::addComponent ($this->componentSelector, $this);

        $this->control    = $this->callMethod ('GetControl', 'object');
        $this->host       = $this->callMethod ('GetHost', 'object');
        $this->selService = $this->callMethod ('GetSelService', 'object');

        VoidEngine::setObjectEvent ($this->control, 'KeyDown', '
            $args = new VoidEngine\KeyEventArgs ($args);

            if ($args->keycode == 46)
            {
                $objects     = VoidEngine\VoidEngine::callMethod ('. $this->componentSelector .', "GetSelectedComponents", "object");
                $firstObject = VoidEngine\VoidEngine::getArrayValue ($objects, 0, "object");
                $className   = substr (explode (".", explode (",", $content)[0])[3], 0, -1);
                $component   = VoidEngine\Components::getComponent ($firstObject);

                $menu    = VoidEngine\VoidStudioAPI::getObjects ("main")["TopMenu"];
                $form    = VoidEngine\VoidEngine::getProperty ($menu->selectedTab, "Text");
                $objects = VoidEngine\VoidStudioAPI::getObjects ("main")["Objects"];

                if ($component instanceof VoidEngine\Form)
                {
                    if ($menu->tabPages->count > 1)
                    {
                        $objects->items->clear ();

                        if (is_array ($GLOBALS["forms"][$form]))
                            foreach ($GLOBALS["forms"][$form] as $id => $name)
                            {
                                $obj = substr ($name, 1, strpos ($name, "]") - 1);

                                if ($obj = VoidEngine\Components::getComponent ($obj))
                                    $obj->dispose ();

                                else VoidEngine\VoidEngine::callMethod ($obj, "Dispose");
                            }

                        unset ($GLOBALS["forms"][$form]);

                        foreach ($menu->tabPages->list as $id => $selector)
                            if (VoidEngine\VoidEngine::getProperty ($selector, "Text") == $form)
                            {
                                VoidEngine\VoidEngine::callMethod ($selector, "Dispose");
                                
                                break;
                            }

                        // VoidEngine\VoidStudioAPI::$objects["main"]["{$form}Designer"]->dispose ();
                    }
                    
                    else VoidEngine\pre (VoidEngine\text ("Нельзя удалить единственную форму проекта"));
                }

                else
                {
                    $cmp = "[$firstObject] $className";

                    $objects->selectedItem = $cmp;
                    $objects->items->remove ($objects->selectedIndex);

                    $component->dispose ();

                    foreach ($GLOBALS["forms"][$form] as $id => $name)
                        if ($name == $cmp)
                        {
                            unset ($GLOBALS["forms"][$form][$id]);

                            break;
                        }
                }
            }
        ');

        VoidEngine::setProperty ($this->control, 'Parent', $parent->selector, 'object');

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

    public function focus ()
    {
        $this->form->focus ();
    }

    public function addComponent (Control $component, string $name)
    {
        VoidEngine::callMethod ($this->host, 'Add', '', $component->selector, 'object', $name, 'string');
        // VoidEngine::setProperty ($component->selector, 'Parent', $this->form->selector, 'object');
        
        $this->focus ();
        $this->callMethod ('SetSelectedComponents', $component->selector, 'object');
    }
}

?>
