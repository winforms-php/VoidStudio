<?php

namespace VoidEngine;

if (!file_exists (dirname (APP_DIR) .'/VoidStudio.lnk'))
    vbs_exec ('
        set objSC = CreateObject ("WScript.Shell").CreateShortcut ("'. text (dirname (APP_DIR) .'/VoidStudio.lnk') .'")
        
        objSC.TargetPath = "'. text (CORE_DIR .'/WinForms PHP.exe') .'"
        objSC.WorkingDirectory  = "'. text (CORE_DIR) .'"
        objSC.Save
    ');

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
    // TODO: настроить использование $precompileVLF
    
    public static function buildProject (string $dir, string $enteringPoint, bool $withVoidFramework = false, bool $exportResources = false, bool $useCaching = false, bool $precompileVLF = false)
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

            file_put_contents ($dir .'/app/'. $item .'.vlf', VLFExporter::constructVLF (VLFExporter::parseObjectsProperties ($designer), $designer, $resourcesDir));
        }

        file_put_contents ($dir .'/app/start.php', str_replace_assoc (file_get_contents (APP_DIR .'/system/presets/build_preset.php'), [
            '\'%use_caching%\''  => $useCaching ? 'true' : 'false',
            '\'%resource_dir%\'' => $resourcesDir !== null ? 'APP_DIR .\'/resources\'' : 'null',
            '%entering_point%'   => $enteringPoint
        ]));

        dir_clean ($dir .'/system/engine/extensions/VLF/cache');
    }

    public static function compileProject (string $save, string $enteringPoint, bool $withVoidFramework = false, bool $precompileVLF = false)
    {
        $savePath   = text (dirname ($save) .'/'. basenameNoExt ($save));
        $vlfImports = '';

        foreach (VoidStudioAPI::getObjects ('main')['Designer__FormsList']->items->names as $id => $item)
        {
            $designer = VoidStudioAPI::getObjects ('main')['Designer__'. $item .'Designer'];

            $vlfImports .= VLFExporter::constructVLF (VLFExporter::parseObjectsProperties ($designer), $designer);
        }

        dir_clean ($savePath);
        dir_copy (CORE_DIR, $savePath);

        unlink ($savePath .'/script.php');
        unlink ($savePath .'/WinForms PHP.exe');

        VoidEngine::compile ($savePath .text ('/'. basename ($save)), text (APP_DIR .'/Icon.ico'), str_replace_assoc (file_get_contents (APP_DIR .'/system/presets/compile_preset.php'), [
            '%VoidEngine%'          => $withVoidFramework ? "define ('FRAMEWORK_DIR', getenv ('AppData') .'\VoidFramework');\n\nif (file_exists (FRAMEWORK_DIR .'/core/VoidEngine.php'))\n\trequire FRAMEWORK_DIR .'/core/VoidEngine.php';\n\nelse message ('VoidEngine not founded');" : VoidStudioBuilder::generateCode (),
            '%vlf_imports%'         => $vlfImports,
            '%entering_point%'      => $enteringPoint,
            'namespace VoidEngine;' => 'nothing ();',
            'VoidEngine\\'          => '',
        ]));
    }

    public static function generateCode (): string
    {
        $code = "/*\n\n\t". join ("\n\t", explode ("\n", file_get_contents (dirname (ENGINE_DIR) .'/license.txt'))) ."\n\n*/\n\n";

        foreach (self::getReferences (ENGINE_DIR .'/VoidEngine.php') as $path)
            $code .= join (array_slice (array_map (function ($line)
            {
                return substr ($line, 0, 7) != 'require' ?
                    $line : '';
            }, file ($path)), 1, -1));

        return $code;
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
