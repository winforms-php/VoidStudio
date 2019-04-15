<?php

namespace VoidEngine;

// ! DEPRECATED

class VLFExporter
{
    public static function parseObjectsProperties (VoidDesigner $designer)
    {
        trigger_error ('Class "VoidEngine\VLFExporter" is deprecated');

        $code = $designer->getSharpCode ('Form');
        // pre ($code);

        $lines              = explode ("\n", $code);
        $current_object     = null;
        $current_object_len = 0;
        $objects            = [];

        foreach ($lines as $id => $line)
        {
            $line = trim ($line);

            if (substr ($line, 0, 3) == '// ' || ($id > 0 && trim ($lines[$id - 1]) == '//' && $line == '//' && trim ($lines[$id + 1]) == '//'))
            {
                $last_object        = $current_object;
                $current_object     = substr ($line, 3);
                $current_object_len = strlen ($current_object) + 5;

                if (strlen ($current_object) == 0) // Объект коллекции
                {
                    if (substr ($last_object, ($len = strlen ($last_object) - 11)) == '_collection')
                    {
                        $i = $len;

                        while ($i >= 0 && is_int ($last_object[$i]))
                            --$i;

                        $current_object = substr ($last_object, 0, $i - 1). (substr ($last_object, $i - 1, -11) + 1) .'_collection';
                    }

                    else $current_object = $last_object .'__0_collection';

                    $current_object_len = 5;
                }

                if (substr ($current_object, 0, 4) == 'Form')
                    $current_object_len = 4;
            }
            
            elseif (substr ($line, 0, $current_object_len) == substr ('this.'. $current_object, 0, $current_object_len) && $current_object !== null)
            {
                $property = str_replace ('.', '->', substr (current (explode (' ', $line)), $current_object_len + 1));
                $value    = array_slice (explode (' = ', $line), 1);

                if (sizeof ($value) == 0) // Методы объектов
                {
                    if (substr ($line, strlen ($line) - 14) == 'new object[] {') // Коллекции
                    {
                        $collection = VoidEngine::getProperty ($designer->getComponentByName ($current_object), $property = explode ('.', $line)[2]);
                        $count      = VoidEngine::getProperty ($collection, 'Count');

                        $collect = [];

                        for ($i = 0; $i < $count; ++$i)
                            $collect[] = new WFExportedData (VoidEngine::exportObject (VoidEngine::getArrayValue ($collection, [[$i, 'int'], 'object'])));

                        $objects[$current_object][$property] = $collect;
                    }
                
                    continue;
                }

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
                            case 'System.Drawing.Font':
                                $value = '['. $args[0] .', '. preg_replace ('/[^0-9\.]/i', '', $args[1]) . (isset ($args[2]) ? (', \''. preg_replace ('/[^a-z]/i', '', substr ($args[2], strrpos ($args[2], '.') + 1)) .'\'') : '') .']';
                            break;

                            case 'System.Windows.Forms.Padding':
                                $value = $args[0];
                            break;

                            default:
                                $value = $args;
                            break;
                        }
                    }

                    else $value = 'new VoidEngine::createObject (new ObjectType (\''. $object .'\'))';
                }

                elseif (is_string ($value))
                {
                    if (substr ($value, strlen ($value) - 1) == ';')
                        $value = substr ($value, 0, -1);

                    if (strpos ($value, ')(resources.GetObject("') !== false)
                    {
                        $object = VoidEngine::getProperty ($designer->getComponentByName ($current_object), $property);
                        $value  = new WFExportedData (VoidEngine::exportObject ($object));
                    }

                    elseif ($value[0] != '"')
                    {
                        $component = $designer->getComponentByName ($current_object);
                        $value     = EngineAdditions::getProperty ($component, $property);

                        if ($value === false)
                            continue;

                        $value = $value['type'] == 'vrsf' ?
                            new WFExportedData ($value['value']) :
                            $value['value'];
                    }
                }

                if ($property == 'Name' && $value == '""')
                    $value = '"'. $current_object .'"';

                $property = str_replace_assoc ($property, [
                    'ForeColor' => 'foregroundColor',
                    'BackColor' => 'backgroundColor'
                ]);

                $objects[$current_object][$property] = $value;
            }
        }

        foreach ($objects as $name => $properties)
            if (is_array ($events = Events::getObjectEvents ($object = $designer->getComponentByName ($name))))
                foreach ($events as $eventName => $event)
                    $objects[$name][$eventName .'Event'] = "function (\$self, \$args)\n\t\t\t{\n\t\t\t\t". join ("\n\t\t\t\t", explode ("\n", Components::getComponentEvent ($object, $eventName))) ."\n\t\t\t}";

        return $objects;
    }

    public static function constructVLF (array $objects, VoidDesigner $designer, string $exportResourcesDir = null)
    {
        // pre ($objects);
        $objectsNames = array_keys ($objects);

        $form     = end ($objects);
        $formName = end ($objectsNames);
        $objects  = array_slice ($objects, 0, -1);
        $export   = is_dir ($exportResourcesDir);

        $vlf = 'Form '. $formName ."\n";

        foreach ($form as $propertyName => $propertyValue)
            if ($propertyValue instanceof WFExportedData)
                if ($export)
                    file_put_contents ($exportResourcesDir .'/'. $formName .'.'. $propertyName .'.vrsf', base64_decode ($propertyValue->data));

                else $vlf .= "\t$propertyName: VoidEngine::importObject ('". $propertyValue->data ."')\n";

            else
            {
                if (is_array ($propertyValue))
                {
                    if (sizeof ($propertyValue) > 0 && $propertyValue[0] instanceof WFExportedData)
                    {
                        if ($export)
                            foreach ($propertyValue as $id => $data)
                                file_put_contents ($exportResourcesDir .'/'. $formName .'.'. $propertyName .'.'. $id .'.vrsf', base64_decode ($data->data));
                        
                        else $vlf .= "\n\t\t%^ namespace VoidEngine;\n\t\t\t\$collection = VoidEngine::getProperty ($formName"."->selector, '$propertyName');\n\n\t\t\tforeach (['". join ('\', \'', array_map (function ($data)
                        {
                            return $data->data;
                        }, $propertyValue)) ."'] as \$id => \$data)\n\t\t\t\tVoidEngine::callMethod (\$collection, 'Add', [VoidEngine::importObject (\$data), 'object']);\n\n";

                        continue;
                    }

                    else $propertyValue = '['. join (', ', $propertyValue) .']';
                }

                if (!$propertyValue)
                    $propertyValue = '\'\'';

                $vlf .= sizeof (explode ("\n", trim ($propertyValue))) > 1 ?
                    "\t$propertyName:^ $propertyValue\n" : "\t$propertyName: $propertyValue\n";
            }

        $vlf .= "\n";

        foreach ($objects as $object => $properties)
        {
            $path = explode ('.', $designer->getComponentClass ($object)->className);
            $vlf .= "\t". end ($path) ." $object\n";

            foreach ($properties as $propertyName => $propertyValue)
                if ($propertyValue instanceof WFExportedData)
                    if ($export)
                        file_put_contents ($exportResourcesDir .'/'. $object .'.'. $propertyName .'.vrsf', base64_decode ($propertyValue->data));

                    else $vlf .= "\t\t$propertyName: VoidEngine::importObject ('". $propertyValue->data ."')\n";

                else
                {
                    if (is_array ($propertyValue))
                    {
                        if (sizeof ($propertyValue) > 0 && $propertyValue[0] instanceof WFExportedData)
                        {
                            if ($export)
                                foreach ($propertyValue as $id => $data)
                                    file_put_contents ($exportResourcesDir .'/'. $object .'.'. $propertyName .'.'. $id .'.vrsf', base64_decode ($data->data));

                            else $vlf .= "\n\t\t%^ namespace VoidEngine;\n\t\t\t\$collection = VoidEngine::getProperty ($object"."->selector, '$propertyName');\n\n\t\t\tforeach (['". join ('\', \'', array_map (function ($data)
                            {
                                return $data->data;
                            }, $propertyValue)) ."'] as \$id => \$data)\n\t\t\t\tVoidEngine::callMethod (\$collection, 'Add', [VoidEngine::importObject (\$data), 'object']);\n\n";

                            continue;
                        }

                        else $propertyValue = '['. join (', ', $propertyValue) .']';
                    }

                    if (!$propertyValue)
                        $propertyValue = '\'\'';

                    $vlf .= sizeof (explode ("\n", trim ($propertyValue))) > 1 ?
                        "\t\t$propertyName:^ $propertyValue\n" : "\t\t$propertyName: $propertyValue\n";
                }

            $vlf .= "\n";
        }

        return $vlf;
    }

    // TODO (+ collections)

    /*public static function constructPHP (array $objects, VoidDesigner $designer, string $exportResourcesDir = null)
    {
        $objectsNames = array_keys ($objects);

        $form     = end ($objects);
        $formName = end ($objectsNames);
        $objects  = array_slice ($objects, 0, -1);
        $export   = false;

        if (is_dir ($exportResourcesDir))
            $export = true;

        $php = '$'. $formName .' = VoidEngine::createObject (unserialize (\''. serialize ($designer->getComponentClass ($formName)) ."'));\n";

        foreach ($form as $propertyName => $propertyValue)
            if ($propertyValue instanceof WFExportedData)
                if ($export)
                    file_put_contents ($exportResourcesDir .'/'. $formName .'.'. $propertyName .'.vrsf', base64_decode ($propertyValue->data));

                else $php .= 'VoidEngine::setProperty ($'. $formName .', \''. $propertyName .'\', VoidEngine::importObject (\''. $propertyValue->data ."'));\n";

            else
            {
                if (is_array ($propertyValue))
                {
                    $php .= "\n";
                    $php .= '$tmp = VoidEngine::getProperty ($'. $formName .', \''. $propertyName ."');\n\n";
                    $php .= 'foreach (['. join (', ', $propertyValue) .'] as $id => $value)' ."\n";
                    $php .= "\tVoidEngine::setArrayValue (\$tmp, \$id, \$value);\n\n";

                    $propertyValue = '$tmp';
                }

                $php .= 'VoidEngine::setProperty ($'. $formName .', \''. $propertyName .'\', '. $propertyValue .");\n";
            }

        $php .= "\n";

        foreach ($objects as $object => $properties)
        {
            $php .= '$'. $object .' = VoidEngine::createObject (unserialize (\''. serialize ($designer->getComponentClass ($object)) ."'));\n";
            $php .= 'VoidEngine::setProperty ($'. $object .', \'Parent\', $'. $formName .');';

            foreach ($properties as $propertyName => $propertyValue)
                if ($propertyValue instanceof WFExportedData)
                    if ($export)
                        file_put_contents ($exportResourcesDir .'/'. $object .'.'. $propertyName .'.vrsf', base64_decode ($propertyValue->data));

                    else $php .= 'VoidEngine::setProperty ($'. $object .', \''. $propertyName .'\', VoidEngine::importObject (\''. $propertyValue->data ."'));\n";

                else
                {
                    if (is_array ($propertyValue))
                    {
                        $php .= "\n";
                        $php .= '$tmp = VoidEngine::getProperty ($'. $object .', \''. $propertyName ."');\n\n";
                        $php .= 'foreach (['. join (', ', $propertyValue) .'] as $id => $value)' ."\n";
                        $php .= "\tVoidEngine::setArrayValue (\$tmp, \$id, \$value);\n\n";

                        $propertyValue = '$tmp';
                    }

                    $php .= 'VoidEngine::setProperty ($'. $object .', \''. $propertyName .'\', '. $propertyValue .");\n";
                }

            $php .= "\n";
        }

        return $php;
    }*/
}

final class WFExportedData
{
    public $data;

    public function __construct (string $data)
    {
        $this->data = $data;
    }
}
