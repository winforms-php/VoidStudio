<?php

namespace VoidEngine;

if (!file_exists (dirname (APP_DIR) .'/VoidStudio.lnk'))
{
    $link = (new \COM ('WScript.Shell'))->CreateShortcut (text (dirname (APP_DIR) .'/VoidStudio.lnk'));
    $link->TargetPath = text (CORE_DIR .'/WinForms PHP.exe');
    $link->WorkingDirectory = text (CORE_DIR);
    $link->Save ();
}

class VoidStudioAPI
{
    public static $objects = [];

    public static function addObjects (string $group, array $objects)
    {
        self::$objects[$group] = array_merge
        (
            isset (self::$objects[$group]) ?
                self::$objects[$group] : [],

            $objects
        );
    }

    public static function getObjects (string $group)
    {
        return isset (self::$objects[$group]) ?
            self::$objects[$group] : false;
    }

    public static function openEventEditor (int $component, string $event, VoidDesigner $designer = null)
    {
        $objects = self::getObjects ('editor');
        $form    = $objects['MainForm'];
        $editor  = $objects['Editor'];

        $editor->helpStorage = [$component, $event];
        $editor->text = Components::getComponentEvent ($component, $event);

        $form->caption = text ('Событие "'. $event .'", объект "'. ($designer === null ? VoidEngine::getProperty ($component, 'Name') : $designer->getComponentName ($component)) .'"');

        $form->showDialog ();
    }

    public static function stopProject ()
    {
        (new Process)->getProcessesByName ('vstmpprj')->foreach (function ($index, $process)
        {
            $process->kill ();
            $process->waitForExit ();
        });
    }

    public static function startProject (TabControl $formsList)
    {
        self::stopProject ();

        VoidStudioBuilder::compileProject (getenv ('Temp') .'/vstmpprj.exe', $formsList->items[0]->text, VoidStudioBuilder::getReferences (ENGINE_DIR .'/VoidEngine.php'), false);

        run (getenv ('Temp') .'/vstmpprj/vstmpprj.exe');
    }
}

class VoidStudioProjectManager
{
    public static function saveProject (string $file): void
    {
        $info = [
            'forms'  => [],
            'events' => []
        ];

        foreach (VoidStudioAPI::getObjects ('main')['Designer__FormsList']->items->names as $item)
        {
            $designer = VoidStudioAPI::getObjects ('main')['Designer__'. $item .'Designer'];

            $info['forms'][$item] = (string) VoidStudioBuilder::appendDesignerData ($designer->getSharpCode ($item, true), $designer);

            foreach ($designer->objects as $name => $objectType)
                if (isset (Components::$events[$designer->getComponentByName ($name)]) && sizeof (Components::$events[$designer->getComponentByName ($name)]) > 0)
                    $info['events'][$item][$name] = Components::$events[$designer->getComponentByName ($name)];
        }

        file_put_contents ($file, gzdeflate (serialize ($info), 9));
    }

    public static function openProject (string $file): void
    {
        $info    = unserialize (gzinflate (file_get_contents ($file)));
        $objects = VoidStudioAPI::getObjects ('main');

        $objects['PropertiesList__List']->selectedObject = null;
        $objects['Designer__FormsList']->items->foreach (function ($index, $value)
        {
            VoidStudioAPI::$objects['main']['Designer__'. $value->text .'Designer']->control->dispose ();
            VoidStudioAPI::$objects['main']['Designer__'. $value->text .'Designer']->form->dispose ();

            unset (VoidStudioAPI::$objects['main']['Designer__'. $value->text .'Designer']);
        });

        $objects['Designer__FormsList']->items->clear ();

        foreach ($info['forms'] as $formName => $form)
        {
            (new WFClass ('WinForms_PHP.WFCompiler', ''))->evalCS ("using System;\nusing System.Windows.Forms;\nusing System.Reflection;\nusing System.Linq;\nusing System.ComponentModel;\n\npublic class CodeEvaler\n{\n\tpublic void EvalCode ()\n\t{\n\t\tVoidControlsParser.parseControls (\"$formName\", (Control) new $formName ());\n\t}\n}\n\n". file_get_contents (APP_DIR .'/system/presets/compile_parser_preset.cs') ."\n\n". (string)($form), true);

            $form = $GLOBALS['__underConstruction'][$formName];
            unset ($GLOBALS['__underConstruction'], $form[$formName]);

            $page     = new TabPage ($formName);
            $designer = new VoidDesigner ($page, $formName, $objects['PropertiesList__List'], $objects['EventsList__ActiveEvents'], $objects['PropertiesPanel__SelectedComponent'], $objects['Designer__FormsList']);

            $designer->initDesigner ();

            $objects['Designer__FormsList']->items->add ($page);
            $objects['Designer__FormsList']->selectedTab = $page;
            
            $objects['PropertiesList__List']->selectedObject = $designer->form;
            $designer->focus ();

            foreach ($form as $name => $selector)
            {
                $designer->addComponent ($selector, $name);

                if (isset ($info['events'][$formName][$name]))
                    foreach ($info['events'][$formName][$name] as $eventName => $event)
                    {
                        Events::reserveObjectEvent ($selector, $eventName);
                        VoidEngine::setObjectEvent ($selector, $eventName, $event);
                    }
            }
        }

        Components::cleanJunk ();
    }
}

class VoidStudioBuilder
{
    public static function compileProject (string $save, string $enteringPoint, array $references, bool $withVoidFramework = false, bool $printSuccessCompile = false): array
    {
        $savePath   = text (dirname ($save) .'/'. basenameNoExt ($save));
        $strClass   = (new WFClass ('System.String', 'mscorlib'))->selector;
        $globalCode = new WFObject (VoidEngine::callMethod ($strClass, ['Concat', 'object'], file_get_contents (APP_DIR .'/system/presets/compile_parser_preset.cs') ."\n\n"));
        $forms      = [];

        foreach (VoidStudioAPI::getObjects ('main')['Designer__FormsList']->items->names as $id => $item)
        {
            $designer   = VoidStudioAPI::getObjects ('main')['Designer__'. $item .'Designer'];
            $globalCode = new WFObject (VoidEngine::callMethod ($strClass, ['Concat', 'object'], $globalCode->selector, self::appendDesignerData ($designer->getSharpCode ($item, true), $designer)->selector));

            $forms[] = $item;

            foreach ($designer->objects as $name => $objectType)
                if (isset (Components::$events[$designer->getComponentByName ($name)]) && sizeof ($events = Components::$events[$designer->getComponentByName ($name)]) > 0)
                    {
                        $name = $name == $item ? 'this' : 'this.'. $name;
                        $str = substr ($globalCode, strrpos ($globalCode, $name, $offset = strrpos ($globalCode, 'this.SuspendLayout();')) ?: $offset);
                        $pos = strpos ($str, "\n");

                        foreach ($events as $eventName => $event)
                        {
                            $eventStr = VoidEngine::callMethod ($strClass, ['Concat', 'object'], $event);
                            
                            $eventStr = '        '. $name .'.'. $eventName .' += (sender, e) => WinForms_PHP.Program.CallEvent (WinForms_PHP.Program.HashByObject ('. $name .'), @"namespace VoidEngine; $self = _c($self); $args = isset ($args) && is_int ($args) && VoidEngine::objectExists ($args) ? new EventArgs ($args) : null; " + WinForms_PHP.Program.getResource ("'. VoidEngine::exportObject ($eventStr) .'"), e);';

                            $globalCode = new WFObject (VoidEngine::callMethod ($globalCode->selector, ['Replace', 'object'], $str, substr ($str, 0, $pos). $eventStr .substr ($str, $pos)));
                        }
                    }
        }

        dir_clean ($savePath);
        dir_copy (CORE_DIR, $savePath);
        
        unlink ($savePath .'/script.php');
        unlink ($savePath .'/WinForms PHP.exe');

/*

$t = VoidEngine::compile ($savePath .text ('/'. basename ($save)), text (APP_DIR .'/Icon.ico'), 'namespace VoidEngine;

'. VoidStudioBuilder::generateCode ($references) .'

if (isset ($GLOBALS[\'__underConstruction\']))
{
    foreach ($GLOBALS[\'__underConstruction\'] as $group => $objects)
        foreach ($objects as $name => $selector)
        {
            $object = new WFObject ($selector);

            try
            {
                $object->name = $name;
            }

            catch (\Throwable $e) {}

            Components::addComponent ($selector, $object);
        }

    $enteringPoint = $GLOBALS[\'__underConstruction\'][\''. $enteringPoint .'\'][\''. $enteringPoint .'\'];
    unset ($GLOBALS[\'__underConstruction\']);

    $APPLICATION->run ($enteringPoint);
}

else throw new \Exception (\'Objects not initialized\');', null, null, null, null, null, str_replace_assoc (file_get_contents (APP_DIR .'/system/presets/compile_main_preset.cs'), [
    '%forms%' => implode ('", "', $forms)
]), $globalCode->selector);

pre ($t);
pre ((string) $globalCode);

return $t;

*/

        $errors = VoidEngine::compile ($savePath .text ('/'. basename ($save)), text (APP_DIR .'/system/icons/Icon.ico'), str_replace_assoc (file_get_contents (APP_DIR .'/system/presets/compile_main_preset.php'), [
            '%VoidEngine%'     => $withVoidFramework ?
                file_get_contents (APP_DIR .'/system/presets/compile_framework_preset.php') :
                VoidStudioBuilder::generateCode ($references),

            '%entering_point%' => $enteringPoint,
        ]), null, null, null, null, null, str_replace_assoc (file_get_contents (APP_DIR .'/system/presets/compile_main_preset.cs'), [
            '%forms%' => join ('", "', $forms)
        ]), $globalCode);

        /*pre ($errors);
        pre ($globalCode->toString ());*/

        $log = VoidStudioAPI::getObjects ('main')['ToolsPanel__LogList'];
        $log->items->add (text ('Проект скомпилирован по пути "'. $save .'". '. (($errorsCount = sizeof ($errors)) > 0 ? ('Обнаружено '. $errorsCount .' ошибок') : 'Ошибок не обнаружено')));

        if ($errorsCount > 0)
        {
            $log->items->addRange (array_map (function ($error)
            {
                return "\t". $error;
            }, $errors));

            messageBox (text ('Обнаружено '. $errorsCount .' ошибок'), text ('Ошибка компиляции'), enum ('System.Windows.Forms.MessageBoxButtons.OK'), enum ('System.Windows.Forms.MessageBoxIcon.Error'));
        }

        elseif ($printSuccessCompile)
            messageBox (text ('Проект успешно скомпилирован'), text ('Успешное компилирование'), enum ('System.Windows.Forms.MessageBoxButtons.OK'), enum ('System.Windows.Forms.MessageBoxIcon.Information'));

        return $errors;
    }

    public static function appendDesignerData (int $code, VoidDesigner $designer): WFObject
    {
        $strClass = (new WFClass ('System.String', 'mscorlib'))->selector;
        $code     = new WFObject ($code);
        $offset   = 0;

        while (($pos = strpos ($code, ')(resources.GetObject("', $offset)) !== false)
        {
            $offset   = $pos + 23;
            $property = explode ('.', substr ($code, $offset, ($end = strpos ($code, '")));', $offset)) - $offset));

            $object = $property[0] == '$this' ?
                $designer->form->selector :
                $designer->getComponentByName ($property[0]);

            foreach (array_slice ($property, 1) as $path)
                $object = VoidEngine::getProperty ($object, $path);

            $code = new WFObject (VoidEngine::callMethod ($code->selector, ['Replace', 'object'], substr ($code, $pos + 2, $end - $pos), 'WinForms_PHP.Program.getResource ("'. VoidEngine::exportObject ($object) .'")'));
        }

        return $code;
    }

    public static function generateCode (array $references, bool $removeNamespaces = true): string
    {
        $code = "/*\n\n\t". join ("\n\t", explode ("\n", file_get_contents (dirname (ENGINE_DIR) .'/license.txt'))) ."\n\n*/\n\n";

        foreach ($references as $path)
            $code .= join (array_slice (array_map (function ($line)
            {
                return substr ($line, 0, 7) != 'require' ?
                    $line : '';
            }, file ($path)), 1, -1));

        return $removeNamespaces ?
            preg_replace ('/namespace [a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*;/', '', $code) : $code;
    }

    public static function getReferences (string $file, bool $parseExtensions = true): array
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

                catch (\Throwable $e)
                {
                    continue;
                }

        if ($parseExtensions)
            if (is_dir (ENGINE_DIR .'/extensions') && is_array ($exts = scandir (ENGINE_DIR .'/extensions')))
                foreach ($exts as $id => $ext)
                    if (is_dir (ENGINE_DIR .'/extensions/'. $ext) && file_exists ($ext = ENGINE_DIR .'/extensions/'. $ext .'/main.php'))
                        $references = array_merge ($references, self::getReferences ($ext, false));

        $references[] = $file;

        return $references;
    }
}

?>
