<?php

namespace VoidEngine;

use VoidBuilder\Builder;

class VoidStudioBuilder
{
    public static function compileProject (string $save, string $enteringPoint, array $references, array $settings = [], bool $printSuccessCompile = false, bool $debug = false): array
    {
        $savePath   = dirname ($save) .'/'. basenameNoExt ($save);
        $globalCode = file_get_contents (APP_DIR .'/system/presets/compile_parser_preset.cs') ."\n\n";
        $eventsCode = '';
        $forms      = [];

        for ($i = 0; $i < 5; ++$i)
            if (!isset ($settings[$i]) || !strlen (trim ($settings[$i])))
                $settings[$i] = null;

        $settings = array_slice ($settings, 0, 5);

        foreach (VoidStudioAPI::getObjects ('main')['Designer__FormsList']->items->names as $id => $item)
        {
            $designer    = VoidStudioAPI::getObjects ('main')['Designer__'. $item .'Designer'];
            $globalCode .= self::appendDesignerData ($designer->getSharpCode ($item), $designer);

            $forms[] = $item;

            foreach ($designer->objects as $name => $objectType)
                if (isset (VoidStudioAPI::$events[$designer->getComponentByName ($name)]) && sizeof ($events = VoidStudioAPI::$events[$designer->getComponentByName ($name)]) > 0)
                    foreach ($events as $eventName => $event)
                        $eventsCode .= 'Events::setObjectEvent ($GLOBALS[\'__underConstruction\'][\''. $item .'\'][\''. $name .'\'], \''. $eventName .'\', function ($self, ...$args)'. "\n" .'{'. "\n". $event ."\n" .'});';
        }

        dir_clean ($savePath);
        dir_copy (CORE_DIR, $savePath);
        
        unlink ($savePath .'/script.php');
        unlink ($savePath .'/VoidCore.exe');

        $errors = VoidEngine::compile ($savePath .'/'. basename ($save), APP_DIR .'/system/icons/Icon.ico', str_replace_assoc (file_get_contents (APP_DIR .'/system/presets/compile_main_preset.php'), [
            '%VoidEngine%' => VoidStudioBuilder::generateCode ($references),

            '%modules%' => (file_exists ($modulesFile = VoidStudioProjectManager::$projectPath .'/modules/Qero.json') && sizeof ($modules = json_decode (file_get_contents ($modulesFile))) ? "require 'qero-packages/autoload.php';\n\n" : "\n\n") . implode ("\n", array_map (function ($module)
            {
                $module = trim (file_get_contents ($module));
    
                if (substr ($module, 0, 2) == '<?')
                    $module = substr ($module, 2);
    
                if (substr ($module, 0, 3) == 'php')
                    $module = substr ($module, 3);
    
                if (substr ($module, -2) == '?>')
                    $module = substr ($module, 0, -2);
    
                return "\$module = <<<'__MODULE'\n\n$module\n\n__MODULE;\n\neval (\$module);";
            }, array_merge (glob (VoidStudioProjectManager::$projectPath .'/modules/*.php'), $debug ? [APP_DIR .'/system/debug/DebugHook.php'] : []))),

            '%events%'         => $eventsCode,
            '%entering_point%' => $enteringPoint,
            '%author_id%'      => sha1 (shell_exec ('wmic csproduct'))
        ]), $settings[0], $settings[1], $settings[2], $settings[3], $settings[4], str_replace_assoc (file_get_contents (APP_DIR .'/system/presets/compile_main_preset.cs'), [
            '%forms%' => join ('", "', $forms)
        ]), $globalCode);

        if (isset ($modules) && sizeof ($modules) > 0)
        {
            dir_delete (APP_DIR .'/Qero/qero-packages');

            $manager = new \Qero\PackagesManager\PackagesManager;

            foreach ($modules as $package)
                $manager->installPackage ($package);

            dir_copy (APP_DIR .'/Qero/qero-packages', $savePath .'/qero-packages');
            dir_delete (APP_DIR .'/Qero/qero-packages');
        }

        // pre ($errors);
        // pre ($globalCode->toString ());

        $log = VoidStudioAPI::getObjects ('main')['ToolsPanel__LogList'];
        $log->items->add ('Проект скомпилирован по пути "'. $save .'". '. (($errorsCount = sizeof ($errors)) > 0 ? ('Обнаружено '. $errorsCount .' ошибок') : 'Ошибок не обнаружено'));

        if ($errorsCount > 0)
        {
            $log->items->addRange (array_map (function ($error)
            {
                return "\t". $error;
            }, $errors));

            messageBox ('Обнаружено '. $errorsCount .' ошибок', 'Ошибка компиляции', enum ('System.Windows.Forms.MessageBoxButtons.OK'), enum ('System.Windows.Forms.MessageBoxIcon.Error'));
        }

        elseif ($printSuccessCompile)
            messageBox ('Проект успешно скомпилирован', 'Успешная компиляция', enum ('System.Windows.Forms.MessageBoxButtons.OK'), enum ('System.Windows.Forms.MessageBoxIcon.Information'));

        return $errors;
    }

    public static function appendDesignerData (string $code, VoidDesigner $designer): string
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

            $code = str_replace (substr ($code, $pos + 2, $end - $pos), 'WinForms_PHP.ZendProgram.getResource ("'. VoidEngine::exportObject ($object) .'")', $code);
        }

        return $code;
    }

    public static function generateCode (array $references, bool $removeNamespaces = true): string
    {
        return Builder::generateCode ($references, $removeNamespaces);
    }

    public static function getReferences (string $file, bool $parseExtensions = true): array
    {
        return Builder::getReferences ($file, $parseExtensions);
    }
}
