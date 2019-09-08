<?php

namespace VoidEngine;

class VLFImporter
{
    static function import (string $vlf, PropertyGrid $propertyGrid, ListBox $eventsList, ComboBox $currentSelectedItem, TabControl $formsList, array $settings = array (
        'strong_line_parser'            => false,
        'ignore_postobject_info'        => true,
        'ignore_unexpected_method_args' => true,
    
        'use_caching' => true,
        'debug_mode'  => false
    )): array
    {
        $objects   = VLFInterpreter::run (new VLFParser ($vlf, $settings));
        $designers = [];

        foreach ($objects as $name => $object)
            if ($object instanceof Form)
            {
                $page = new TabPage ($name);
                $designers[$object->selector] = new VoidDesigner ($page, $name, $propertyGrid, $eventsList, $currentSelectedItem, $formsList, $object);

                $designers[$object->selector]->form->text = $object->text;

                VoidStudioAPI::addObjects ('main', ['Designer__'. $name .'Designer' => $designers[$object->selector]]);

                $formsList->items->add ($page);
                $formsList->selectedTab = $page->selector;
            }

            elseif ($object instanceof Control)
                try
                {
                    $parent = $object->parent;
                    while (!isset ($designers[$parent->selector]) && $parent->parent)
                        $parent = $parent->parent;

                    if (isset ($designers[$parent->selector]))
                        $designers[$parent->selector]->addComponent ($object->selector, $name);
                }

                catch (\Throwable $e) {}

        return $designers;
    }
}
