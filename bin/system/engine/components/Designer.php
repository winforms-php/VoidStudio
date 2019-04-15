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

    public function __construct (Component $parent, string $formName = 'form', PropertyGrid $propertyGrid, EventGrid $eventsList, ComboBox $currentSelectedItem, TabControl $formsList, $form = null)
    {
        $this->form = $form === null ? new Form :
            EngineAdditions::coupleSelector ($form);

        if (!is_object ($this->form))
            throw new \Exception ('$form param in "VoidEngine\VoidDesigner" constructor must be instance of "VoidEngine\WFObject" ("VoidEngine\Form") or be object selector');

        $this->propertyGrid        = $propertyGrid;
        $this->eventsList          = $eventsList;
        $this->currentSelectedItem = $currentSelectedItem;
        $this->formsList           = $formsList;

        $this->selector = VoidEngine::createObject ('WinForms_PHP.FormDesigner4', null, $this->form->selector, $formName);
        Components::addComponent ($this->selector, $this);

        $this->form->name = $formName;

        if ($form === null)
        {
            $this->form->text = $formName;
            $this->form->size = [400, 360];
        }

        $this->control = $this->callMethod ('GetControl');
        $this->objects[$formName] = ['System.Windows.Forms.Form', 'System.Windows.Forms'];

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
            _c('. $eventsList->selector .')->selectedObject = $firstObject;
            _c('. $currentSelectedItem->selector .')->selectedItem = _c('. $this->selector .')->getComponentName ($firstObject);

            if (isset (Components::$events[$firstObject]) && sizeof (Components::$events[$firstObject]) > 0)
                foreach (Components::$events[$firstObject] as $eventName => $event)
                    _c('. $eventsList->selector .')->getEventByName ($eventName)->value = text ("(добавлено)");

            _c('. $eventsList->selector .')->refresh ();
        ');
    }

    public function initDesigner (): void
    {
        $this->componentAddedEvent = function ($self, $args)
        {
            if (isset ($GLOBALS['new_component']))
            {
                $self->setComponentToHistory ($GLOBALS['new_component'][1], $GLOBALS['new_component'][0]);
                $components = VoidStudioAPI::getObjects ('main')['PropertiesPanel__SelectedComponent'];

                $components->items->clear ();
                $components->items->addRange (array_keys ($self->objects));

                $components->selectedItem = $GLOBALS['new_component'][0];
                $self->setSelectedComponents ($args->component);

                unset ($GLOBALS['new_component']);
            }
        };

        $this->rightClickEvent = function ($self, $args)
        {
            $delItem = new ToolStripMenuItem (text ('Удалить'));
            $delItem->image = (new Image)->loadFromFile (text (APP_DIR .'/system/icons/Delete_16x.png'));
            $delItem->clickEvent = function () use ($self)
            {
                $self->removeSelected ();
            };

            $toFrontItem = new ToolStripMenuItem (text ('На передний план'));
            $toFrontItem->image = (new Image)->loadFromFile (text (APP_DIR .'/system/icons/Front_16x.png'));
            $toFrontItem->clickEvent = function () use ($self)
            {
                $self->bringToFrontSelected ();
            };

            $toBackItem = new ToolStripMenuItem (text ('На задний план'));
            $toBackItem->image = (new Image)->loadFromFile (text (APP_DIR .'/system/icons/Back_16x.png'));
            $toBackItem->clickEvent = function () use ($self)
            {
                $self->sendToBackSelected ();
            };

            $infoItem = new ToolStripMenuItem (text ('Отладочная информация'));
            $infoItem->image = (new Image)->loadFromFile (text (APP_DIR .'/system/icons/Debug_16x.png'));
            $infoItem->clickEvent = function () use ($self)
            {
                $self->getSelectedComponents ()->foreach (function ($value) use ($self)
                {
                    pre ($value instanceof Component ? $value : $value->toString () ."\nSelector: ". $value->selector);

                    // if ($value->getType ()->toString () == 'System.Windows.Forms.Form')
                        pre ($self->getSharpCode ($self->form->name));
                });
            };

            $menu = new ContextMenuStrip;
            $menu->items->addRange ([
                $delItem, '-',
                $toFrontItem, $toBackItem, '-',
                $infoItem
            ]);

            $menu->show ($self->form, $self->form->pointToClient (VoidEngine::createObject ('System.Drawing.Point', 'System.Drawing', $args->x, $args->y)));
        };

        VoidStudioAPI::addObjects ('main', ['Designer__'. $this->form->name .'Designer' => $this]);
    }

    public function focus (): void
    {
        $this->form->focus ();
    }

    public function getSharpCode (string $formName, bool $asObject = false): string
    {
        $code = $this->callMethod (['GetSharpCode', 'object'], $formName);

        $code = VoidEngine::callMethod ($code, ['Replace', 'object'], 'public class '. $this->form->name .' : '. $this->form->name, 'public class '. $this->form->name .' : System.Windows.Forms.Form');
        $code = VoidEngine::callMethod ($code, ['Replace', 'object'], '    private ', '    public ');

        return $asObject ?
            $code : VoidEngine::callMethod ($code, 'ToString');
    }

    public function createComponent (array $component, string $componentName): int
    {
        $this->objects[$componentName] = $component;
        $selector = VoidEngine::createObject (...$component);

        $this->callMethod ('AddComponent', $selector, $componentName);

        return $selector;
    }

    public function setComponentToHistory (array $component, string $componentName): void
    {
        $this->objects[$componentName] = $component;
    }

    public function addComponent (int $selector, string $componentName): void
    {
        $this->objects[$componentName] = [VoidEngine::callMethod (VoidEngine::callMethod ($selector, 'GetType'), 'ToString'), 'auto'];

        $this->callMethod ('AddComponent', $selector, $componentName);
    }

    public function removeComponent (int $component): void
    {
        unset ($this->objects[$this->getComponentName ($component)]);

        $this->callMethod ('RemoveComponent', $component);
    }

    public function removeComponentHistoryByName (string $name): void
    {
        unset ($this->objects[$name]);
    }

    public function removeSelected (): void
    {
        $toUnset = [];

        foreach ($this->getSelectedComponents ()->list as $object)
        {
            if ($object->getType ()->toString () != 'System.Windows.Forms.Form')
                $toUnset[] = $this->getComponentName ($object->selector);

            else
            {
                if ($this->formsList->items->count > 1)
                {
                    if (messageBox (text ('Вы действительно хотите удалить форму "'. $this->form->name .'"?'), text ('Подтвердите действие'), enum ('System.Windows.Forms.MessageBoxButtons.YesNo'), enum ('System.Windows.Forms.MessageBoxIcon.Question')) == 6)
                    {
                        foreach ($this->objects as $name => $obj)
                            unset ($this->objects[$name]);

                        unset ($this->formsList->items[array_flip ($this->formsList->items->names)[$form = $this->getComponentName ($object->selector)]]);

                        /*$this->form->dispose ();
                        VoidEngine::callMethod ($this->control, 'Dispose');*/
                        $this->callMethod ('DeleteSelected');

                        $designer = VoidStudioAPI::getObjects ('main')['Designer__'. $this->formsList->selectedTab->text .'Designer'];
                        
                        $designer->propertyGrid->selectedObject = $designer->form;
                        $designer->setSelectedComponents ($designer->form);

                        unset (VoidStudioAPI::$objects['main']['Designer__'. $form .'Designer']);

                        return;
                    }
                }

                else
                {
                    messageBox (text ('Нельзя удалить единственную форму проекта'), text ('Ошибка удаления'), enum ('System.Windows.Forms.MessageBoxButtons.OK'), enum ('System.Windows.Forms.MessageBoxIcon.Error'));

                    return;
                }
            }
        }

        foreach ($toUnset as $name)
            unset ($this->objects[$name]);

        $this->callMethod ('DeleteSelected');

        foreach ($this->objects as $objectName => $object)
            if (!is_int ($this->getComponentByName ($objectName)))
                unset ($this->objects[$objectName]);

        $this->currentSelectedItem->items->clear ();
        $this->currentSelectedItem->items->addRange (array_keys ($this->objects));
        $this->currentSelectedItem->selectedItem = $this->form->name;
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
