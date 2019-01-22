<?php

namespace VoidEngine;

class VoidStudioAPI
{
    static $objects = [];
    //static $project;

    /**
     * Временно бесполезно
     */

    static function createProject (string $name, Form $projectMainForm)
    {
        /*if (is_dir ($path = STUDIO_DIR .'/projects/'. $name))
            return false;

        else
        {
            self::$project = $name;

            mkdir ($path);

            file_put_contents ($path .'/project.json', json_encode ([
                'Form1' =>
                [
                    $projectMainForm->selector
                ]
            ], JSON_PRETTY_PRINT));
        }*/
    }

    static function saveProject ()
    {
        /*if (!is_dir ($path = STUDIO_DIR .'/projects/'. self::$project))
            return false;

        else
        {
            $data = [];

            foreach ($GLOBALS['forms'] as $form => $objects)
                foreach ($objects as $id => $object)
                    $data[$form][] = substr ($object, 1, strpos ($object, ']') - 1);
            
            file_put_contents ($path .'/project.json', json_encode ($data, JSON_PRETTY_PRINT));
        }*/
    }

    /**
     * TODO all
     */
    /*static function openProject (string $name)
    {
        if (!is_dir ($path = STUDIO_DIR .'/projects/'. $name))
            return false;

        else
        {
            $data = json_decode (file_get_contents ($path .'/project.json'));
            $GLOBALS['forms'] = [];

            foreach ($data as $form => $objects)
                foreach ($objects as $id => $object)
                    $GLOBALS['forms'][] = '['. $object .'] ?';
        }
    }*/

    static function addObjects (string $group, array $objects)
    {
        self::$objects[$group] = array_merge
        (
            isset (self::$objects[$group]) ?
                self::$objects[$group] : [],

            $objects
        );
    }

    static function getObjects (string $group)
    {
        return isset (self::$objects[$group]) ?
            self::$objects[$group] : false;
    }

    static function loadObjectEvents (Control $object, ListBox $list)
    {
        $list->items->clear ();

        $type  = VoidEngine::callMethod ($object->selector, 'GetType', 'object');
        $props = VoidEngine::callMethod ($type, 'GetEvents', 'object');
        $len   = VoidEngine::getProperty ($props, 'Length', 'int');

        for ($i = 0; $i < $len; ++$i)
        {
            $index = VoidEngine::getArrayValue ($props, $i, 'object');
            $name  = VoidEngine::getProperty ($index, 'Name', 'string');

            $list->items->add ($name);
        }
    }

    static function openEventEditor (Component $component, string $event)
    {
        $form   = self::$objects['editor']['MainForm'];
        $editor = self::$objects['editor']['Editor'];

        $editor->helpStorage = [$component->selector, $event];
        $editor->text = Components::getComponentEvent ($component->selector, $event);

        $form->caption = text ('Событие "'. $event .'", ['. $component->selector .'] '. $component->caption);

        $form->show ();
    }
}

class VoidStudioBuilder
{
    static function constructVLF (array $objects = null)
    {
        $return = '';

        if ($objects === null)
            foreach ($GLOBALS['forms'] as $form => $components)
                foreach ($components as $id => $component)
                    $objects[] = substr ($component, 1, strpos ($component, ']') - 1);

        if (is_array ($objects))
            foreach ($objects as $id => $selector)
            {
                $object = Components::getComponent ($selector);
                $name   = $object->name;
                $parent = $object->parent;

                if (!Components::getComponent ($parent))
                    $parent = null;

                else $parent = Components::getComponent ($parent)->name;

                $class = get_class ($object);
                $class = substr ($class, ($pos = strrpos ($class, '\\')) !== false ? $pos + 1 : 0);

                $return .= "$class $name". ($parent !== null ?
                    " ($parent)" : ''
                ) ."\n\tcaption: base64_decode ('". base64_encode ($object->caption) ."')\n\tbounds: [". $object->x .', '. $object->y .', '. $object->w .', '. $object->h ."]\n";

                if (isset ($GLOBALS['AvailableObjects'][$selector]))
                {
                    $properties = array_diff ($GLOBALS['AvailableObjects'][$selector], [
                        'caption', 'bounds'
                    ]);

                    foreach ($properties as $propertyName => $property)
                        $return .= "\t$propertyName: base64_decode ('". base64_encode ($property['value']) ."')\n";
                }

                if (isset (Components::$events[$selector]))
                {
                    $return .= "\n";

                    foreach (Components::$events[$selector] as $name => $event)
                        $return .= "\t{$name}Event:^ function (\$self, \$args)\n\t\t{\n\t\t\t". implode ("\n\t\t\t", explode ("\n", $event)) ."\n\t\t}\n";
                }

                $return .= "\n";
            }

        return $return;
    }
}

?>
