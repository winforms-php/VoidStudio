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

    public static function openEventEditor (int $component, string $event)
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

            $info['forms'][$item] = VoidStudioBuilder::appendResources ($designer->getSharpCode ($item), $designer);

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
            (new WFClass ('WinForms_PHP.WFCompiler', ''))->evalCS ("using System;\nusing System.Windows.Forms;\n\npublic class CodeEvaler\n{\n\tpublic void EvalCode ()\n\t{\n\t\tVoidControlsParser.parseControls (\"". $formName ."\", (Control) new ". $formName ." ());\n\t}\n}\n\n". file_get_contents (APP_DIR .'/system/presets/compile_parser_preset.cs') ."\n\n". $form, true);

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
    public static function compileProject (string $save, string $enteringPoint, bool $withVoidFramework = false): array
    {
        $savePath   = text (dirname ($save) .'/'. basenameNoExt ($save));
        $globalCode = file_get_contents (APP_DIR .'/system/presets/compile_parser_preset.cs') ."\n\n";
        $forms      = [];
        $events     = [];

        foreach (VoidStudioAPI::getObjects ('main')['Designer__FormsList']->items->names as $id => $item)
        {
            $designer = VoidStudioAPI::getObjects ('main')['Designer__'. $item .'Designer'];

            $globalCode .= self::appendResources ($designer->getSharpCode ($item), $designer);
            $forms[] = $item;

            foreach ($designer->objects as $name => $objectType)
                if (isset (Components::$events[$designer->getComponentByName ($name)]) && sizeof (Components::$events[$designer->getComponentByName ($name)]) > 0)
                    $events[$item][$name] = Components::$events[$designer->getComponentByName ($name)];
        }

        dir_clean ($savePath);
        dir_copy (CORE_DIR, $savePath);
        
        unlink ($savePath .'/script.php');
        unlink ($savePath .'/WinForms PHP.exe');

        return VoidEngine::compile ($savePath .text ('/'. basename ($save)), text (APP_DIR .'/Icon.ico'), str_replace_assoc (file_get_contents (APP_DIR .'/system/presets/compile_main_preset.php'),[
            '%VoidEngine%'     => $withVoidFramework ?
                file_get_contents (APP_DIR .'/system/presets/compile_framework_preset.php') :
                VoidStudioBuilder::generateCode (),

            '%entering_point%' => $enteringPoint,
            '%events%'         => base64_encode (gzdeflate (serialize ($events), 9)),
        ]), null, null, null, null, null, str_replace_assoc (file_get_contents (APP_DIR .'/system/presets/compile_main_preset.cs'), [
            '%forms%' => join ('", "', $forms)
        ]), $globalCode);
    }

    public static function appendResources (string $code, VoidDesigner $designer)
    {
        $offset = 0;

        while (($pos = strpos ($code, ')(resources.GetObject("', $offset)) !== false)
        {
            $offset   = $pos + 23;
            $property = explode ('.', substr ($code, $offset, ($end = strpos ($code, '")));', $offset)) - $offset));

            $object = $property[0] == '$this' ?
                $designer->form->selector :
                $designer->getComponentByName ($property[0]);

            foreach (array_slice ($property, 1) as $path)
                $object = VoidEngine::getProperty ($object, $path);

            $code = substr ($code, 0, $pos + 2) .'WinForms_PHP.Program.getResource ("'. VoidEngine::exportObject ($object) .'")'. substr ($code, $end + 2);
        }

        return $code;
    }

    public static function generateCode (bool $removeNamespaces = true): string
    {
        $code = "/*\n\n\t". join ("\n\t", explode ("\n", file_get_contents (dirname (ENGINE_DIR) .'/license.txt'))) ."\n\n*/\n\n";

        foreach (self::getReferences (ENGINE_DIR .'/VoidEngine.php') as $path)
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

?>
