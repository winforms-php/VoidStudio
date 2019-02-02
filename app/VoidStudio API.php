<?php

namespace VoidEngine;

class VoidStudioAPI
{
    static $objects = [];

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

        $type  = VoidEngine::callMethod ($object->selector, 'GetType');
        $props = VoidEngine::callMethod ($type, 'GetEvents');
        $len   = VoidEngine::getProperty ($props, 'Length');

        for ($i = 0; $i < $len; ++$i)
        {
            $index = VoidEngine::getArrayValue ($props, $i);
            $name  = VoidEngine::getProperty ($index, 'Name');

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
    static function parseObjectsProperties (VoidDesigner $designer)
    {
        $code = $designer->getSharpCode ();

        $lines              = explode ("\n", $code);
        $current_object     = null;
        $current_object_len = 0;
        $objects            = [];

        foreach ($lines as $id => $line)
        {
            $line = trim ($line);

            if (substr ($line, 0, 3) == '// ')
            {
                $current_object     = substr ($line, 3);
                $current_object_len = strlen ($current_object) + 5;

                if (substr ($current_object, 0, 4) == 'Form')
                    $current_object_len = 4;
            }
            
            elseif (substr ($line, 0, $current_object_len) == substr ('this.'. $current_object, 0, $current_object_len) && $current_object !== null)
            {
                $property = str_replace ('.', '->', substr (current (explode (' ', $line)), $current_object_len + 1));
                $value    = array_slice (explode (' = ', $line), 1);

                if (sizeof ($value) == 0) // Методы объектов
                    continue;

                $value = join (' = ', $value);

                if (substr ($value, 0, 4) == 'new ')
                {
                    $object = substr ($value, 4);
                    $args   = [];

                    if (($pos = strpos ($object, '(')) !== false)
                    {
                        $args   = explode (', ', substr ($object, $pos + 1, strrpos ($object, ');') - $pos - 1));
                        $object = substr ($object, 0, $pos);

                        switch ($object)
                        {
                            case 'System.Drawing.Point':
                            case 'System.Drawing.Size':
                                $value = '['. join (', ', $args) .']';
                            break;

                            default:
                                pre ('Unknown object "'. $object .'" with arguments "'. json_encode ($args) .'"');
                            break;
                        }
                    }
                }

                if (is_string ($value))
                {
                    if (substr ($value, strlen ($value) - 1) == ';')
                        $value = substr ($value, 0, -1);

                    // ((System.Drawing.Icon)(resources.GetObject("$this.Icon")))

                    if (strpos ($value, ')(resources.GetObject("') !== false)
                    {
                        $object = VoidEngine::getProperty ($designer->getComponentByName ($current_object), $property);
                        $value  = new WFExportedData (gzdeflate (VoidEngine::exportObject ($object), 9));
                    }

                    elseif ($value[0] != '"')
                        try
                        {
                            $value = VoidEngine::getProperty ($designer->getComponentByName ($current_object), [$property, 'int']);
                        }

                        catch (\Throwable $e) {}
                }

                $objects[$current_object][$property] = $value;
            }
        }

        return $objects;
    }

    static function constructVLF (array $objects, VoidDesigner $designer)
    {
        $objectsNames = array_keys ($objects);

        $form     = end ($objects);
        $formName = end ($objectsNames);
        $objects  = array_slice ($objects, 0, -1);

        $vlf = 'Form '. $formName ."\n";

        foreach ($form as $propertyName => $propertyValue)
            if ($propertyValue instanceof WFExportedData)
                $vlf .= "\t$propertyName: VoidEngine::importObject ('". gzinflate ($propertyValue->data) ."')\n";

            else $vlf .= "\t$propertyName: $propertyValue\n";

        $vlf .= "\n";

        foreach ($objects as $object => $properties)
        {
            $path = explode ('.', $designer->getComponentClass ($object)->className);
            $vlf .= "\t". end ($path) ." $object\n";

            foreach ($properties as $propertyName => $propertyValue)
                if ($propertyValue instanceof WFExportedData)
                    $vlf .= "\t\t$propertyName: VoidEngine::importObject ('". gzinflate ($propertyValue->data) ."')\n";

                else $vlf .= "\t\t$propertyName: $propertyValue\n";

            $vlf .= "\n";
        }

        return $vlf;
    }
}

final class WFExportedData
{
    public $data;

    public function __construct (string $data)
    {
        $this->data = $data;
    }
}

/*

public class Form_WF_PHP : System.Windows.Forms.Form
{
    private System.Windows.Forms.Button Button1549124598227599;
    private Form_WF_PHP()
    {
        this.InitializeComponent();
    }
    private void InitializeComponent()
    {
        this.Button1549124598227599 = new System.Windows.Forms.Button();
        this.SuspendLayout();
        // 
        // Button1549124598227599
        // 
        this.Button1549124598227599.Location = new System.Drawing.Point(0, 0);
        this.Button1549124598227599.Name = "Button1549124598227599";
        this.Button1549124598227599.Size = new System.Drawing.Size(75, 23);
        this.Button1549124598227599.TabIndex = 0;
        // 
        // Form1
        // 
        this.ClientSize = new System.Drawing.Size(284, 261);
        this.Controls.Add(this.Button1549124598227599);
        this.Name = "Form1";
        this.Text = "Form1";
        this.ResumeLayout(false);
    }
}


*/

?>
