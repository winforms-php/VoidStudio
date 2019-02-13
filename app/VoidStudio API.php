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

    static function openEventEditor (int $component, string $event)
    {
        $objects = self::getObjects ('editor');
        $form    = $objects['MainForm'];
        $editor  = $objects['Editor'];

        $editor->helpStorage = [$component, $event];
        $editor->text = Components::getComponentEvent ($component, $event);

        $form->caption = text ('Событие "'. $event .'", объект "'. VoidEngine::getProperty ($component, 'Name') .'"');

        $form->showDialog ();
    }
}

class VoidStudioBuilder
{
    static function buildProject (string $dir, string $enteringPoint, bool $withVoidFramework = false, bool $exportResources = false, bool $useCaching = false, bool $precompileVLF = false)
    {
        dir_clean ($dir .'/system');
        dir_clean ($dir .'/app');

        dir_copy (dirname (ENGINE_DIR), $dir .'/system');

        if ($withVoidFramework)
            dir_delete ($dir .'/system/engine');

        $resourcesDir = null;

        if ($exportResources)
        {
            $resourcesDir = $dir .'/app/resources';

            dir_create ($resourcesDir);
        }

        foreach (VoidStudioAPI::getObjects ('main')['Designer__FormsList']->items->names as $id => $item)
        {
            $designer = VoidStudioAPI::getObjects ('main')['Designer__'. $item .'Designer'];

            if ($item == $enteringPoint)
                $item = 'main';

            file_put_contents ($dir .'/app/'. $item .'.vlf', VoidStudioBuilder::constructVLF (VoidStudioBuilder::parseObjectsProperties ($designer), $designer, $resourcesDir));
        }

        file_put_contents ($dir .'/app/start.php', "<?php\n\nnamespace VoidEngine;\n\nVLFInterpreter::\$throw_errors = false;\n\nconst APP_DIR = __DIR__;\nchdir (APP_DIR);\n\nforeach (glob ('*.vlf') as \$id => \$path)\n\tif ((\$path = basename (\$path)) != 'main.vlf')\n\t\tVLFInterpreter::run (new VLFParser (__DIR__ .'/'. \$path, [\n\t\t\t\t'strong_line_parser'            => false,\n\t\t\t\t'ignore_postobject_info'        => true,\n\t\t\t\t'ignore_unexpected_method_args' => true,\n\n\t\t\t\t'use_caching' => ". ($useCaching ? 'true' : 'false') ."\n\t\t\t]), ". ($resourcesDir !== null ? 'APP_DIR .\'/resources\'' : 'null') .");\n\n\$APPLICATION->run (VLFInterpreter::run (new VLFParser (__DIR__ .'/main.vlf', [\n\t'strong_line_parser'            => false,\n\t'ignore_postobject_info'        => true,\n\t'ignore_unexpected_method_args' => true,\n\n\t'use_caching' => ". ($useCaching ? 'true' : 'false') ."\n]), ". ($resourcesDir !== null ? 'APP_DIR .\'/resources\'' : 'null') .")['$enteringPoint']);\n\n?>\n");

        dir_clean ($dir .'/system/core/debug');
        dir_clean ($dir .'/system/core/extensions/VLF/cache');
    }

    static function compileProject (string $save, string $enteringPoint, bool $withVoidFramework = false, bool $precompileVLF = false)
    {
        $vlfImports = '';

        foreach (VoidStudioAPI::getObjects ('main')['Designer__FormsList']->items->names as $id => $item)
        {
            $designer = VoidStudioAPI::getObjects ('main')['Designer__'. $item .'Designer'];

            $vlfImports .= VoidStudioBuilder::constructVLF (VoidStudioBuilder::parseObjectsProperties ($designer), $designer);
        }

        $vlfImports = "\$vlf = <<<'VLF'\n\n$vlfImports\n\nVLF;";

        VoidEngine::compile ($save, text (APP_DIR) .'/Icon.ico', $code = str_replace (
            [
                'namespace VoidEngine;',
                'VoidEngine\\'
            ],
            
            [
                'nothing ();',
                ''
            ],
            
            "\$code = <<<'CODE'\n\n". ($withVoidFramework ? "define ('FRAMEWORK_DIR', getenv ('AppData') .'\VoidFramework');\n\nif (file_exists (FRAMEWORK_DIR .'/core/VoidEngine.php'))\n\trequire FRAMEWORK_DIR .'/core/VoidEngine.php';\n\nelse message ('VoidEngine not founded');" : VoidStudioBuilder::generateCode ()) ."\n\nCODE;\n\n@eval (\$code);\n\n$vlfImports\n\nVLFInterpreter::\$throw_errors = false;\n\n\$APPLICATION->run (VLFInterpreter::run (new VLFParser (\$vlf, [\n\t'strong_line_parser'            => false,\n\t'ignore_postobject_info'        => true,\n\t'ignore_unexpected_method_args' => true,\n\n\t'use_caching' => false\n]))['$enteringPoint']);"
        ));

        // file_put_contents (dirname ($save) .'/tmp.php', $code);
    }

    static function parseObjectsProperties (VoidDesigner $designer)
    {
        $code = $designer->getSharpCode ();

        $lines              = explode ("\n", $code);
        $size               = sizeof ($lines);
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

                    /*{
                        $collect = [];

                        for ($i = $id + 1; $i < $size && substr ($lines[$i] = trim ($lines[$i]), strlen ($lines[$i]) - 3) != '});'; ++$i)
                            $collect[] = substr ($lines[$i], 0, -1);

                        if (strlen ($tmp = substr ($lines[$i], 0, -3)) > 0)
                            $collect[] = $tmp;

                        $id = $i;
                        $objects[$current_object][$property] = $value;
                    }*/
                
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

                    else $value = 'new VoidEngine::createObject (WFObject (\''. $object .'\', \''. join ('.', array_slice (explode ('.', $object), 0, -1)) .'\'))';
                }

                elseif (is_string ($value))
                {
                    if (substr ($value, strlen ($value) - 1) == ';')
                        $value = substr ($value, 0, -1);

                    // ((System.Drawing.Icon)(resources.GetObject("$this.Icon")))

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

    static function constructVLF (array $objects, VoidDesigner $designer, string $exportResourcesDir = null)
    {
        $objectsNames = array_keys ($objects);

        $form     = end ($objects);
        $formName = end ($objectsNames);
        $objects  = array_slice ($objects, 0, -1);
        $export   = false;

        if (is_dir ($exportResourcesDir))
            $export = true;

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

                    $vlf .= sizeof (explode ("\n", trim ($propertyValue))) > 1 ?
                        "\t\t$propertyName:^ $propertyValue\n" : "\t\t$propertyName: $propertyValue\n";
                }

            $vlf .= "\n";
        }

        return $vlf;
    }

    // TODO (+ collections)

    /*static function constructPHP (array $objects, VoidDesigner $designer, string $exportResourcesDir = null)
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

    static function generateCode (): string
    {
        $code = "/*\n\n\t". join ("\n\t", explode ("\n", file_get_contents (dirname (ENGINE_DIR) .'/license.txt'))) ."\n\n*/\n\n";

        foreach (self::getReferences (ENGINE_DIR .'/VoidEngine.php') as $id => $path)
            $code .= join (array_slice (array_map (function ($line)
            {
                return substr ($line, 0, 7) != 'require' ?
                    $line : '';
            }, file ($path)), 1, -1));

        return $code;
    }

    static function getReferences (string $file, bool $parseExtensions = true): array
    {
        $references = [];

        foreach (file ($file) as $id => $line)
            if (substr ($line, 0, 7) == 'require')
                try
                {
                    $begin = strpos ($line, "'");
                    $end   = strrpos ($line, "'") - $begin + 1;

                    $references = array_merge ($references, self::getReferences (dirname ($file) .'/'. eval ('namespace VoidEngine; return '. substr ($line, $begin, $end) .';'), false));
                }

                catch (\Throwable $e) {}

        if ($parseExtensions)
            if (is_dir (ENGINE_DIR .'/extensions') && is_array ($exts = scandir (ENGINE_DIR .'/extensions')))
                foreach ($exts as $id => $ext)
                    if (is_dir (ENGINE_DIR .'/extensions/'. $ext) && file_exists ($ext = ENGINE_DIR .'/extensions/'. $ext .'/main.php'))
                        $references = array_merge ($references, self::getReferences ($ext, false));

        $references[] = $file;

        return $references;
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

?>
