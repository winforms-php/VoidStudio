<?php

namespace VoidEngine;

class VoidStudioProjectManager
{
    public static string $projectPath = '';

    public static function createProject (string $name = 'default'): bool
    {
        if (!is_dir ($dir = 'C:/Users/'. USERNAME .'/Documents/VoidStudio'))
            dir_create ($dir);

        if (is_dir ($dir = $dir .'/'. $name))
        {
            if (messageBox ('Проект "'. $name .'" уже создан. Перезаписать его?', 'Подтвердите действие', enum ('System.Windows.Forms.MessageBoxButtons.YesNo'), enum ('System.Windows.Forms.MessageBoxIcon.Question')) == 6)
                dir_clean ($dir);

            else return false;
        }

        else dir_create ($dir);

        self::$projectPath = $dir;

        VoidStudioAPI::getObjects ('main')['MainForm']->caption    = $name .' — VoidStudio';
        VoidStudioAPI::getObjects ('modules')['MainForm']->caption = $name .' — модули проекта';
        VoidStudioAPI::getObjects ('main')['ToolsPanel__LogList']->items->add ('Проект "'. $name .'" успешно создан');

        dir_create ($dir .'/modules');
        // copy (__DIR__ .'/../Qero/Qero.phar', $dir .'/Qero.phar');

        // require $dir .'/Qero.phar';

        return true;
    }

    public static function saveProject (string $file): void
    {
        $info = [
            'forms'   => [],
            'events'  => [],
            'objects' => []
        ];

        foreach (VoidStudioAPI::getObjects ('main')['Designer__FormsList']->items->names as $item)
        {
            $designer = VoidStudioAPI::getObjects ('main')['Designer__'. $item .'Designer'];

            $info['forms'][$item]   = VoidStudioBuilder::appendDesignerData ($designer->getSharpCode ($item), $designer);
            $info['objects'][$item] = array_keys ($designer->objects);
            $info['events'][$item]  = VoidStudioAPI::$events[$item] ?? null;
        }

        file_put_contents ($file, gzdeflate (serialize ($info), 9));

        if (replaceSl (self::$projectPath) != replaceSl (dirname ($file)))
            dir_copy (self::$projectPath .'/modules', dirname ($file) .'/modules');

        VoidStudioAPI::getObjects ('main')['ToolsPanel__LogList']->items->add ('Проект успешно сохранён');
    }

    public static function openProject (string $file): void
    {
        messageBox ('В настоящий момент пересматривается алгоритм открытия проектов, поэтому возможны многочисленные баги (в т.ч. невозможность компиляции открытого проекта)'. "\n\nБудет исправлено в ближайшее время\nС уважением, разработчики проекта WinForms PHP", 'Предупреждение об ошибках', enum ('System.Windows.Forms.MessageBoxButtons.OK'), enum ('System.Windows.Forms.MessageBoxIcon.Warning'));

        self::$projectPath = dirname ($file); 

        $info    = unserialize (gzinflate (file_get_contents ($file)));
        $objects = VoidStudioAPI::getObjects ('main');

        foreach ($objects['Designer__FormsList']->items->names as $form)
        {
            $designer = VoidStudioAPI::$objects['main']['Designer__'. $form .'Designer'];

            $designer->form->dispose ();
            $designer->control->dispose ();

            unset (VoidStudioAPI::$objects['main']['Designer__'. $form .'Designer'], $designer);
        }

        $objects['PropertiesList__List']->selectedObject = null;
        $objects['Designer__FormsList']->items->clear ();

        foreach ($info['forms'] as $formName => $form)
        {
            $sourceForm = (new WFClass ('WinForms_PHP.WFCompiler', ''))->evalCS ("using System;\nusing System.Windows.Forms;\nusing System.Reflection;\nusing System.Linq;\nusing System.ComponentModel;\n\npublic class CodeEvaler\n{\n\tpublic object EvalCode ()\n\t{\n\t\tForm form = new $formName ();\n\n\t\tVoidControlsParser.ParseControlsForOpening (\"$formName\", (Control) form);\n\n\t\treturn form;\n\t}\n}\n\n". file_get_contents (APP_DIR .'/system/presets/compile_parser_preset.cs') ."\n\n". (string)($form), true);

            $form = $GLOBALS['__underConstruction'][$formName];
            unset ($GLOBALS['__underConstruction'], $form[$formName]);

            $formObjects = array_flip ($info['objects'][$formName]);

            foreach ($form as $name => $selector)
                if (!isset ($formObjects[$name]))
                    unset ($form[$name]);

            $page = new TabPage ($formName);
            $page->backgroundColor = clWhite;

            $objects['Designer__FormsList']->items->add ($page);

            $designer = new VoidDesigner ($page, $formName, $objects['PropertiesList__List'], $objects['PropertiesPanel__SelectedComponent'], $objects['Designer__FormsList'], $sourceForm);
            $designer->initDesigner ();

            // VoidStudioAPI::$objects['main']['Designer__'. $formName .'Designer'] = $designer;
            VoidStudioAPI::$events[$formName] = $info['events'][$formName] ?? null;

            foreach ($form as $name => $selector)
                $designer->addComponent ($selector, $name);
        }

        // Components::cleanJunk ();

        $objects['Designer__FormsList']->selectedTab = $page;

        $objects['PropertiesPanel__SelectedComponent']->items->clear ();
        $objects['PropertiesPanel__SelectedComponent']->items->addRange (array_keys ($designer->objects));
        $objects['PropertiesPanel__SelectedComponent']->selectedItem = $formName;

        VoidStudioAPI::getObjects ('main')['MainForm']->caption = basenameNoExt ($file) .' — VoidStudio';

        $objects['PropertiesList__List']->selectedObject = $designer->form;
        $designer->focus ();
    }
}
