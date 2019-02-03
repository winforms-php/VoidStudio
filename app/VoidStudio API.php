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
                            case 'System.Drawing.Point':
                            case 'System.Drawing.Size':
                                $value = $args;
                            break;

                            case 'System.Drawing.Font':
                                $value = '['. $args[0] .', '. preg_replace ('/[^0-9\.]/i', '', $args[1]) .']';
                            break;

                            case 'System.Windows.Forms.Padding':
                                $value = $args[0];
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
                        $value  = new WFExportedData (VoidEngine::exportObject ($object));
                    }

                    elseif ($value[0] != '"')
                        try
                        {
                            $value = VoidEngine::getProperty ($designer->getComponentByName ($current_object), [$property, 'int']);
                        }

                        catch (\Throwable $e)
                        {
                            try
                            {
                                $value = VoidEngine::getProperty ($designer->getComponentByName ($current_object), [$property, 'color']);

                                $property = str_replace_assoc ($property, [
                                    'ForeColor' => 'foregroundColor',
                                    'BackColor' => 'backgroundColor'
                                ]);
                            }

                            catch (\Throwable $e)
                            {
                                pre ('Unknown constant "'. $value .'" (property "'. $property .'")');
                            }
                        }
                }

                if ($property == 'Name' && $value == '""')
                    $value = '"'. $current_object .'"';

                $objects[$current_object][$property] = $value;
            }
        }

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

                $vlf .= "\t$propertyName: $propertyValue\n";
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

                    $vlf .= "\t\t$propertyName: $propertyValue\n";
                }

            $vlf .= "\n";
        }

        return $vlf;
    }

    // TODO (+ collections)

    static function constructPHP (array $objects, VoidDesigner $designer, string $exportResourcesDir = null)
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
    }

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

                catch (\Exception $e) {}

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

this.ListBox1549187752284736.Items.AddRange(new object[] {
    "1",
    "2",
    "3"});

public class Form_WF_PHP : System.Windows.Forms.Form
{
    private System.Windows.Forms.TabControl TabControl1549197084431037;
    private System.Windows.Forms.TabPage ;
    private System.Windows.Forms.TabPage ;
    private Form_WF_PHP()
    {
        this.InitializeComponent();
    }
    private void InitializeComponent()
    {
        this.TabControl1549197084431037 = new System.Windows.Forms.TabControl();
        this. = new System.Windows.Forms.TabPage();
        this. = new System.Windows.Forms.TabPage();
        this.TabControl1549197084431037.SuspendLayout();
        this.SuspendLayout();
        // 
        // TabControl1549197084431037
        // 
        this.TabControl1549197084431037.Controls.Add(this.);
        this.TabControl1549197084431037.Controls.Add(this.);
        this.TabControl1549197084431037.Location = new System.Drawing.Point(0, 0);
        this.TabControl1549197084431037.Name = "TabControl1549197084431037";
        this.TabControl1549197084431037.SelectedIndex = 0;
        this.TabControl1549197084431037.Size = new System.Drawing.Size(200, 100);
        this.TabControl1549197084431037.TabIndex = 0;
        // 
        // 
        // 
        this..Location = new System.Drawing.Point(4, 22);
        this..Name = "";
        this..Padding = new System.Windows.Forms.Padding(3);
        this..Size = new System.Drawing.Size(192, 74);
        this..TabIndex = 0;
        this..UseVisualStyleBackColor = true;
        // 
        // 
        // 
        this..Location = new System.Drawing.Point(4, 22);
        this..Name = "";
        this..Padding = new System.Windows.Forms.Padding(3);
        this..Size = new System.Drawing.Size(192, 74);
        this..TabIndex = 1;
        this..UseVisualStyleBackColor = true;
        // 
        // Form1
        // 
        this.ClientSize = new System.Drawing.Size(284, 261);
        this.Controls.Add(this.TabControl1549197084431037);
        this.Name = "Form1";
        this.Text = "Form1";
        this.TabControl1549197084431037.ResumeLayout(false);
        this.ResumeLayout(false);
    }
}

*/

?>
