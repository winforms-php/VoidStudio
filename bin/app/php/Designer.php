<?php

namespace VoidEngine;

class VoidDesigner extends Component
{
    protected $form;
    protected $control;
    protected $objects;

    protected $propertyGrid;
    protected $currentSelectedItem;
    protected $formsList;

    public function __construct (Component $parent, string $formName = 'form', PropertyGrid $propertyGrid, ComboBox $currentSelectedItem, TabControl $formsList, $form = null)
    {
        $this->form = $form === null ? new Form :
            EngineAdditions::coupleSelector ($form);

        if (!is_object ($this->form))
            throw new \Exception ('$form param in "VoidEngine\VoidDesigner" constructor must be instance of "VoidEngine\WFObject" ("VoidEngine\Form") or be object selector');

        $this->propertyGrid        = $propertyGrid;
        $this->currentSelectedItem = $currentSelectedItem;
        $this->formsList           = $formsList;

        $this->selector = \VoidCore::createObject ('WinForms_PHP.FormDesigner5', null, $this->form->selector, $formName);
        Components::addComponent ($this->selector, $this);

        $this->form->name = $formName;

        if ($form === null)
        {
            $this->form->text = $formName;
            $this->form->size = [400, 360];
        }

        $this->control = $this->callMethod ('GetControl');
        $this->objects[$formName] = ['System.Windows.Forms.Form', 'System.Windows.Forms'];

        \VoidCore::setProperty ($this->control, 'Parent', $parent->selector);

        $this->selectionChangedEvent = function ()
        {
            $object = $this->getPrimarySelection ()->selector;
            
            $this->propertyGrid->selectedObject = $object;
            $this->currentSelectedItem->selectedItem = $this->getComponentName ($object);
        };

        $this->createUniqueMethodNameEvent = function ($self, $args)
        {
            return $this->getComponentName ($args->component->selector) . '_' . $args->eventDescriptor->Name;
        };
        
        $this->getCompatibleMethodsEvent = function ()
        {
            $form = $this->formsList->selectedTab->text;
            
            return getNetArray ('System.String', ClassWorker::getAvailableClassMethods (VoidStudioAPI::$events[$form] ?? '', $form))->selector;
        };
        
        $this->showCodeEvent = function ($self, $args)
        {
            VoidStudioAPI::openEventEditor ($this->propertyGrid->selectedObject->selector, $args->methodName, $args->eventDescriptor, $form = c('Designer__FormsList')->selectedTab->text, VoidStudioAPI::getObjects ('main')['Designer__'. $form .'Designer']);
        };

        $this->freeMethodEvent = function ($self, $args)
        {
            $object = $this->propertyGrid->selectedObject->selector;

            Events::removeObjectEvent ($object, $method = $args->eventDescriptor->Name);
            unset (VoidStudioAPI::$events[$object][$method]);
        };
    }

    public function initDesigner (): void
    {
        $this->componentAddedEvent = function ($self, $args)
        {
			if (!isset ($GLOBALS['new_component']))
			{
                $name = $args->component->getType ()->toString ();

                // pre (\VoidCore::getProperty ($args->component->selector, 'Name'));
                
				$GLOBALS['new_component'] = [$this->getComponentName ($args->component->selector), [$name, substr ($name, 0, strrpos ($name, '.'))]];
			}
			
			$this->setComponentToHistory ($GLOBALS['new_component'][1], $GLOBALS['new_component'][0]);
			$components = VoidStudioAPI::getObjects ('main')['PropertiesPanel__SelectedComponent'];

			$components->items->clear ();
			$components->items->addRange (array_keys ($this->objects));

			$components->selectedItem = $GLOBALS['new_component'][0];
			$this->setSelectedComponents ($args->component);

			unset ($GLOBALS['new_component']);
        };
		
		$this->componentRemovedEvent = function ($self, $args)
		{
			$name = $this->getComponentName ($args->component->selector);
			unset ($this->objects[$name]);

			foreach ($this->objects as $objectName => $object)
				if (!is_int ($this->getComponentByName ($objectName)))
					unset ($this->objects[$objectName]);

			$this->currentSelectedItem->items->clear ();
			$this->currentSelectedItem->items->addRange (array_keys ($this->objects));
			$this->currentSelectedItem->selectedItem = $this->getComponentName ($this->getPrimarySelection ()->selector);
		};

        // ! Отредактировал что-то здесь?
        // ! Не забудь отредактировать и в main.vlf

        $this->rightClickEvent = function ($self, $args)
        {
            $delItem = new ToolStripMenuItem ('Удалить');
            $delItem->image = (new Image)->loadFromFile (APP_DIR .'/system/icons/Delete_16x.png');
            $delItem->shortcutKeys = 46;
            $delItem->clickEvent   = fn () => $this->removeSelected ();

            $toFrontItem = new ToolStripMenuItem ('На передний план');
            $toFrontItem->image = (new Image)->loadFromFile (APP_DIR .'/system/icons/Front_16x.png');
            // $toFrontItem->shortcutKeys = 131142;
            $toFrontItem->clickEvent = fn () => $self->doAction ('bringToFront');

            $toBackItem = new ToolStripMenuItem ('На задний план');
            $toBackItem->image = (new Image)->loadFromFile (APP_DIR .'/system/icons/Back_16x.png');
            // $toBackItem->shortcutKeys = 131138;
            $toBackItem->clickEvent = fn () => $self->doAction ('sendToBack');

            if ((new WFObject (\VoidCore::typeof ('System.Windows.Forms.Control')))->isAssignableFrom ($this->propertyGrid->selectedObject->getType ()))
            {
                $locked = ($locker = (new WFClass ('System.ComponentModel.TypeDescriptor', 'System'))
                    ->getProperties ($object = $this->propertyGrid->selectedObject->selector)['Locked'])
                    ->getValue ($object);
                
                $desItem = new ToolStripMenuItem ($locked ? 'Разблокировать' : 'Заблокировать');
                $desItem->image = (new Image)->loadFromFile (APP_DIR .'/system/icons/'. ($locked ? 'Unlock' : 'Lock') .'_16x.png');
                // $desItem->shortcutKeys = 131148;
                $desItem->clickEvent = function () use ($object, $locker, $locked)
                {
                    $locker->setValue ($object, !$locked);

                    $this->propertyGrid->refresh ();
                };
            }

            $selectAllItem = new ToolStripMenuItem ('Выделить всё');
            $selectAllItem->image = (new Image)->loadFromFile (APP_DIR .'/system/icons/SelectAll_16x.png');
            $selectAllItem->shortcutKeys = 131137;
            $selectAllItem->clickEvent = fn () => $self->doAction ('selectAll');
			
            $cutItem = new ToolStripMenuItem ('Вырезать');
            $cutItem->image = (new Image)->loadFromFile (APP_DIR .'/system/icons/Cut_16x.png');
            $cutItem->shortcutKeys = 131160;
            $cutItem->clickEvent   = fn () => $self->doAction ('cut');
			
            $copyItem = new ToolStripMenuItem ('Копировать');
            $copyItem->image = (new Image)->loadFromFile (APP_DIR .'/system/icons/Copy_16x.png');
            $copyItem->shortcutKeys = 131139;
            $copyItem->clickEvent   = fn () => $self->doAction ('copy');
			
            $pasteItem = new ToolStripMenuItem ('Вставить');
            $pasteItem->image = (new Image)->loadFromFile (APP_DIR .'/system/icons/Paste_16x.png');
            $pasteItem->shortcutKeys = 131158;
            $pasteItem->clickEvent   = fn () => $self->doAction('paste');
			
            $undoItem = new ToolStripMenuItem ('Отменить');
            $undoItem->image = (new Image)->loadFromFile (APP_DIR .'/system/icons/Undo_16x.png');
            $undoItem->shortcutKeys = 131162;
            $undoItem->clickEvent   = fn () => $self->undoEngine->undo ();
			
            $redoItem = new ToolStripMenuItem ('Повторить');
            $redoItem->image = (new Image)->loadFromFile (APP_DIR .'/system/icons/Redo_16x.png');
            $redoItem->shortcutKeys = 131161;
            $redoItem->clickEvent   = fn () => $self->undoEngine->redo ();

            $infoItem = new ToolStripMenuItem ('Отладочная информация');
            $infoItem->image = (new Image)->loadFromFile (APP_DIR .'/system/icons/Debug_16x.png');
            $infoItem->clickEvent = function () use ($self)
            {
                $self->getSelectedComponents ()->foreach (function ($value) use ($self)
                {
                    pre ($value instanceof Component ? $value : $value->toString () ."\nSelector: ". $value->selector);

                    if ((new WFObject (\VoidCore::typeof ('System.Windows.Forms.Form')))->isAssignableFrom ($value->getType ()))
                    {
                        $code = $self->getVoidCode ($self->form->name, false);

                        pre ($self->getSharpCode ($self->form->name));
                        pre ('namespace VoidEngine;'. "\n\n" .'return new class' . substr ($code, 38) . ';');
                    }
                });
            };

            $menu = new ContextMenuStrip;

            $menu->items->addRange (isset ($desItem) ? [
                $selectAllItem, $copyItem, $pasteItem, $cutItem, $delItem, '-',
                $toFrontItem, $toBackItem, $desItem, '-',
				$undoItem, $redoItem, '-',
                $infoItem
            ] : [
                $selectAllItem, $copyItem, $pasteItem, $cutItem, $delItem, '-',
                $toFrontItem, $toBackItem, '-',
				$undoItem, $redoItem, '-',
                $infoItem
            ]);

            $menu->show ($self->form, $self->form->pointToClient (\VoidCore::createObject ('System.Drawing.Point', false, $args->x, $args->y)));
        };

        VoidStudioAPI::addObjects ('main', ['Designer__'. $this->form->name .'Designer' => $this]);
    }

    public function focus (): void
    {
        $this->form->focus ();
    }

    public function getSharpCode (string $formName): string
    {
        $code = $this->callMethod (['GetSharpCode', 'object'], $formName);

        $code = \VoidCore::callMethod ($code, ['Replace', 'object'], 'public class '. $this->form->name .' : '. $this->form->name, 'public class '. $this->form->name .' : System.Windows.Forms.Form');
        $code = \VoidCore::callMethod ($code, ['Replace', 'object'], '    private ', '    public ');

        return \VoidCore::callMethod ($code, 'ToString');
    }

    public function createComponent (array $component, string $componentName): int
    {
        $this->objects[$componentName] = $component;
        $selector = \VoidCore::createObject (...$component);

        $this->callMethod ('AddComponent', $selector, $componentName);

        return $selector;
    }

    public function setComponentToHistory (array $component, string $componentName): void
    {
        $this->objects[$componentName] = $component;
    }

    public function addComponent (int $selector, string $componentName): void
    {
        $this->objects[$componentName] = [\VoidCore::callMethod (\VoidCore::callMethod ($selector, 'GetType'), 'ToString'), false];

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
                    if (messageBox ('Вы действительно хотите удалить форму "'. $this->form->name .'"?', 'Подтвердите действие', enum ('System.Windows.Forms.MessageBoxButtons.YesNo'), enum ('System.Windows.Forms.MessageBoxIcon.Question')) == 6)
                    {
                        foreach ($this->objects as $name => $obj)
                            unset ($this->objects[$name]);

                        unset ($this->formsList->items[array_flip ($this->formsList->items->names)[$form = $this->getComponentName ($object->selector)]]);

                        /*$this->form->dispose ();
                        \VoidCore::callMethod ($this->control, 'Dispose');*/
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
                    messageBox ('Нельзя удалить единственную форму проекта', 'Ошибка удаления', enum ('System.Windows.Forms.MessageBoxButtons.OK'), enum ('System.Windows.Forms.MessageBoxIcon.Error'));

                    return;
                }
            }
        }

        foreach ($toUnset as $name)
            unset ($this->objects[$name]);

        $this->doAction ('delete');

        foreach ($this->objects as $objectName => $object)
            if (!is_int ($this->getComponentByName ($objectName)))
                unset ($this->objects[$objectName]);

        $this->currentSelectedItem->items->clear ();
        $this->currentSelectedItem->items->addRange (array_keys ($this->objects));
        $this->currentSelectedItem->selectedItem = $this->getComponentName ($this->getPrimarySelection ()->selector);
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
