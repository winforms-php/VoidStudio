<?php

namespace VoidEngine;

/**
 * @package VoidDesigner
 * 
 * ! Разработан для работы в рамках VoidStudio
 * ! Для портирования в свой проект - измените обозначеный ниже код
 * 
 * TODO переписать класс
 * 
 */

class VoidDesigner extends Component
{
    protected $form;
    protected $control;
    protected $host;

    public function __construct (Control $parent, string $formName = 'form')
    {
        $this->componentSelector = VoidEngine::createObject (new WFObject ('WinForms_PHP.FormDesigner', false, true), [$parent->selector, 'object'], [$formName, 'string']);
        Components::addComponent ($this->componentSelector, $this);

        $this->form    = $this->callMethod (['GetForm', 'object']);
        $this->control = $this->callMethod (['GetControl', 'object']);

        /**
         * * Удаление объектов или формы по нажатию кнопки "Del"
         * Изменить:
         * 
         * @var menu
         * @var objects
         * 
         * А так же систему парсинга названий объектов форм ($GLOBALS["forms"]...)
         * 
         */

        /*VoidEngine::setObjectEvent ($this->control, 'KeyDown', '
            namespace VoidEngine;

            $args = new KeyEventArgs ($args);

            if ($args->keycode == 46)
            {
                $objects     = VoidEngine::callMethod ('. $this->componentSelector .', ["GetSelectedComponents", "object"]);
                $firstObject = VoidEngine::getArrayValue ($objects, [0, "object"]);
                $content     = VoidEngine::callMethod ($firstObject, ["ToString", "string"]);
                $className   = substr (explode (".", explode (",", $content)[0])[3], 0, -1);
                $component   = Components::getComponent ($firstObject);

                $menu    = VoidStudioAPI::getObjects ("main")["TopMenu"];
                $form    = VoidEngine::getProperty ($menu->selectedTab, "Text");
                $objects = VoidStudioAPI::getObjects ("main")["Objects"];

                if ($component instanceof Form)
                {
                    if ($menu->tabPages->count > 1)
                    {
                        $objects->items->clear ();

                        if (is_array ($GLOBALS["forms"][$form]))
                            foreach ($GLOBALS["forms"][$form] as $id => $name)
                            {
                                $obj = substr ($name, 1, strpos ($name, "]") - 1);

                                if ($obj = Components::getComponent ($obj))
                                    $obj->dispose ();

                                else VoidEngine::callMethod ($obj, "Dispose");
                            }

                        unset ($GLOBALS["forms"][$form]);

                        foreach ($menu->tabPages->list as $id => $selector)
                            if (VoidEngine::getProperty ($selector, "Text") == $form)
                            {
                                VoidEngine::callMethod ($selector, "Dispose");
                                
                                break;
                            }

                        // VoidStudioAPI::$objects["main"]["{$form}Designer"]->dispose ();

                        Components::cleanJunk ();
                    }
                    
                    else pre (text ("Нельзя удалить единственную форму проекта"));
                }

                else
                {
                    $cmp = "[$firstObject] $className";

                    $objects->selectedItem = $cmp;
                    $objects->items->remove ($objects->selectedIndex);

                    if (($id = array_search ($cmp, $GLOBALS["forms"][$form])) !== false)
                        unset ($GLOBALS["forms"][$form][$id]);

                    $component->dispose ();
                    Components::cleanJunk ();
                }
            }
        ');*/

        /**
         * * Выделение компонентов на форме
         * Изменить последнюю строку с указанием выделенного объекта
         */
        /*VoidEngine::setObjectEvent ($this->selService, 'SelectionChanged', '
            namespace VoidEngine;

            $objects = VoidEngine::callMethod ('. $this->componentSelector .', ["GetSelectedComponents", "object"]);

            $firstObject = VoidEngine::getArrayValue ($objects, 0, "object");
            $content     = VoidEngine::callMethod ($firstObject, "ToString");
            $className   = substr (explode (".", explode (",", $content)[0])[3], 0, -1);
			
			VoidEngine::setProperty ('. $propertyGrid->selector .', "SelectedObject", [$firstObject, "object"]);

            VoidStudioAPI::getObjects ("main")["Objects"]->selectedItem = "[$firstObject] ". VoidEngine::getProperty ([$firstObject, "Name");
            VoidStudioAPI::loadObjectEvents (Components::getComponent ($firstObject), VoidStudioAPI::getObjects ("main")["LeftMenu__EventsList"]);
        ');*/
    }

    public function updateHost (): void
    {
        $this->callMethod ('UpdateHost');
    }

    public function focus (): void
    {
        VoidEngine::callMethod ($this->form, 'Focus');
    }

    public function getSharpCode (): string
    {
        return $this->callMethod (['GetSharpCode', 'string']);
    }

    public function createComponent (WFObject $component, string $componentName): int
    {
        return $this->callMethod (['CreateComponent', 'object'], [VoidEngine::objectType ($component), 'object'], [$componentName, 'string']);
    }

    public function removeComponent (int $component): void
    {
        $this->callMethod ('RemoveComponent', [$component, 'object']);
    }

    public function renameComponent (int $component, string $name): void
    {
        $this->callMethod ('RenameComponent', [$component, 'object'], [$name, 'string']);
    }

    public function getComponentName (int $component): string
    {
        return $this->callMethod (['GetComponentName', 'string'], [$component, 'object']);
    }
}

?>
