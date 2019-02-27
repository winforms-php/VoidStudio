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

class VoidStudioBuilder
{
    public static function compileProject (string $save, string $enteringPoint, bool $withVoidFramework = false): array
    {
        $savePath   = text (dirname ($save) .'/'. basenameNoExt ($save));
        $globalCode = file_get_contents (APP_DIR .'/system/presets/compile_parser_preset.cs');
        $forms      = [];
        $events     = [];

        foreach (VoidStudioAPI::getObjects ('main')['Designer__FormsList']->items->names as $id => $item)
        {
            $designer = VoidStudioAPI::getObjects ('main')['Designer__'. $item .'Designer'];

            $globalCode .= $designer->getSharpCode ($item);
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
