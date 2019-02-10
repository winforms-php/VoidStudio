<?php

namespace VoidEngine;

/**
 * @package VoidDesigner
 * 
 * ! Разработан для работы в рамках VoidStudio
 * 
 */

class VoidDesigner extends Component
{
    protected $form;
    protected $control;
    protected $objects;

    protected $propertyGrid;
    protected $eventsList;
    protected $currentSelectedItem;
    protected $formsList;

    public function __construct (Control $parent, string $formName = 'form', PropertyGrid $propertyGrid, ListBox $eventsList, ComboBox $currentSelectedItem, TabControl $formsList)
    {
        $this->form = new Form;

        $this->propertyGrid        = $propertyGrid;
        $this->eventsList          = $eventsList;
        $this->currentSelectedItem = $currentSelectedItem;
        $this->formsList           = $formsList;

        $this->selector = VoidEngine::createObject (new ObjectType ('WinForms_PHP.FormDesigner4', false, true), $this->form->selector, $formName);
        Components::addComponent ($this->selector, $this);

        $this->form->name = $formName;
        $this->form->text = $formName;
        $this->form->size = [400, 360];

        $this->control = $this->callMethod ('GetControl');

        $this->objects[$formName] = new ObjectType ('System.Windows.Forms.Form');

        VoidEngine::setProperty ($this->control, 'Parent', $parent->selector);

        VoidEngine::setObjectEvent ($this->control, 'KeyDown', '
            namespace VoidEngine;

            $args = new EventArgs ($args);

            if ($args->keycode == 46)
                _c('. $this->selector .')->removeSelected ();
        ');

        VoidEngine::setObjectEvent ($this->selector, 'SelectionChanged', '
            namespace VoidEngine;

            $objects     = VoidEngine::callMethod ('. $this->selector .', "GetSelectedComponents");
            $firstObject = VoidEngine::getArrayValue ($objects, 0);
            
            _c('. $propertyGrid->selector .')->selectedObject = $firstObject;
            _c('. $currentSelectedItem->selector .')->selectedItem = VoidEngine::getProperty ($firstObject, "Name");
        
            $events = Events::getObjectEvents ($firstObject);
            _c('. $eventsList->selector .')->items->clear ();

            if (is_array ($events))
                _c('. $eventsList->selector .')->items->addRange (array_keys ($events));
        ');
    }

    public function focus (): void
    {
        $this->form->focus ();
    }

    public function getSharpCode (): string
    {
        return $this->callMethod ('GetSharpCode');
    }

    public function createComponent (ObjectType $component, string $componentName): int
    {
        $this->objects[$componentName] = $component; // VoidEngine::objectType ($component)
        $selector = VoidEngine::createObject ($component);

        $this->callMethod ('AddComponent', $selector, $componentName);

        return $selector;
    }

    /*public function addComponent (int $selector, string $componentName): void
    {
        $this->callMethod ('AddComponent', $selector, $componentName);
    }*/

    public function removeComponent (int $component): void
    {
        unset ($this->objects[$this->getComponentName ($component)]);

        $this->callMethod ('RemoveComponent', $component);
    }

    public function removeSelected (): void
    {
        $objects = VoidEngine::callMethod ($this->selector, 'GetSelectedComponents');
        $size    = VoidEngine::getProperty ($objects, 'Length');
        $toUnset = [];

        for ($i = 0; $i < $size; ++$i)
        {
            $object = VoidEngine::getArrayValue ($objects, $i);

            if (VoidEngine::callMethod (VoidEngine::callMethod ($object, 'GetType'), 'ToString') != 'System.Windows.Forms.Form')
                $toUnset[] = $this->getComponentName ($object);

            else
            {
                if ($this->formsList->items->count > 1)
                {
                    $toUnset[] = $this->getComponentName ($object);

                    $this->formsList->items->remove (array_flip ($this->formsList->items->names)[$form = VoidEngine::getProperty ($object, 'Name')]);

                    $this->form->dispose ();
                    $this->callMethod ('DeleteSelected');

                    $designer = VoidStudioAPI::getObjects ('main')['Designer__'. $this->formsList->selectedTab->text .'Designer'];
                    
                    $this->propertyGrid->selectedObject = $designer->form->selector;
                    $designer->setSelectedComponents ($designer->form->selector);

                    unset (VoidStudioAPI::$objects['main']['Designer__'. $form .'Designer']);
                    Components::cleanJunk ();

                    return;
                }

                else
                {
                    pre (text ('Нельзя удалить единственную форму проекта'));

                    return;
                }
            }
        }

        foreach ($toUnset as $id => $name)
            unset ($this->objects[$name]);

        $this->currentSelectedItem->items->clear ();
        $this->currentSelectedItem->items->addRange (array_keys ($this->objects));

        $this->callMethod ('DeleteSelected');

        Components::cleanJunk ();
    }

    public function renameComponent (int $component, string $name, string $fromName = null): void
    {
        if ($fromName === null)
            $fromName = $this->getComponentName ($component);

        $info = $this->objects[$fromName];
        unset ($this->objects[$fromName]);
        $this->objects[$name] = $info;

        $this->callMethod ('RenameComponent', $component, $name);
    }

    public function getComponentName (int $component): string
    {
        return $this->callMethod ('GetComponentName', $component);
    }

    public function getComponentByName (string $name)
    {
        return $this->callMethod ('GetComponentByName', $name);
    }

    public function getComponentClass (string $name)
    {
        return isset ($this->objects[$name]) ?
            $this->objects[$name] : false;
    }

    public function addProperty (int $selector, string $name, int $value, bool $readOnly, string $category, string $description, bool $visible)
    {
        $this->callMethod ('AddProperty', $selector, $name, $value, $readOnly, $category, $description, $visible);
    }

    public function removeProperty (int $selector, string $name)
    {
        $this->callMethod ('RemoveProperty', $selector, $name);
    }
}

class VoidDesigner__deprecated extends Component
{
    protected $form;
    protected $control;
    protected $objects;

    public function __construct (Control $parent, string $formName = 'form', PropertyGrid $propertyGrid = null, ListBox $eventsList = null)
    {
        $this->componentSelector = VoidEngine::createObject (new WFObject ('WinForms_PHP.FormDesigner', false, true), $parent->selector, $formName);
        Components::addComponent ($this->componentSelector, $this);

        $this->form    = $this->callMethod ('GetForm');
        $this->control = $this->callMethod ('GetControl');

        $this->objects[$formName] = new WFObject ('System.Windows.Forms.Form');

        VoidEngine::setProperty ($this->form, 'Text', $formName);

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

        /*VoidEngine::setObjectEvent ($this->componentSelector, 'KeyDown', '
            namespace VoidEngine;

            $args = new KeyEventArgs ($args);

            if ($args->keycode == 46)
            {
                $objects     = VoidEngine::callMethod ('. $this->componentSelector .', "GetSelectedComponents");
                $firstObject = VoidEngine::getArrayValue ($objects, 0);
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
         * 
         */

        if ($propertyGrid !== null)
            VoidEngine::setObjectEvent ($this->componentSelector, 'SelectionChanged', '
                namespace VoidEngine;

                $objects     = VoidEngine::callMethod ('. $this->componentSelector .', "GetSelectedComponents");
                $firstObject = VoidEngine::getArrayValue ($objects, 0);
                
                _c('. $propertyGrid->selector .')->selectedObject = $firstObject;
                c("PropertiesPanel__SelectedComponent")->selectedItem = VoidEngine::getProperty ($firstObject, "Name");
            '. ($eventsList !== null ?
            '
                $events = Events::getObjectEvents ($firstObject);
                _c('. $eventsList->selector .')->items->clear ();

                if (is_array ($events))
                    _c('. $eventsList->selector .')->items->addRange (array_keys ($events));
            '
        : ''));
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
        return $this->callMethod ('GetSharpCode');
    }

    public function createComponent (WFObject $component, string $componentName): int
    {
        $this->objects[$componentName] = $component;

        return $this->callMethod ('CreateComponent', VoidEngine::objectType ($component), $componentName);
    }

    public function addComponent (int $selector, string $componentName): void
    {
        $this->callMethod ('AddComponent', $selector, $componentName);
    }

    public function removeComponent (int $component): void
    {
        unset ($this->objects[$this->getComponentName ($component)]);

        $this->callMethod ('RemoveComponent', $component);
    }

    public function renameComponent (int $component, string $name, string $fromName = null): void
    {
        if ($fromName === null)
            $fromName = $this->getComponentName ($component);

        $info = $this->objects[$fromName];
        unset ($this->objects[$fromName]);
        $this->objects[$name] = $info;

        $this->callMethod ('RenameComponent', $component, $name);
    }

    public function getComponentName (int $component): string
    {
        return $this->callMethod ('GetComponentName', $component);
    }

    public function getComponentByName (string $name): int
    {
        return $this->callMethod ('GetComponentByName', $name);
    }

    public function getComponentClass (string $name)
    {
        return isset ($this->objects[$name]) ?
            $this->objects[$name] : false;
    }
}

?>
