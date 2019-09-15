<?php

namespace VoidEngine;

class VoidStudioAPI
{
    public static array $objects = [];
    public static array $events  = [];
    public static $project;

    public static function addObjects (string $group, array $objects)
    {
        self::$objects[$group] = array_merge
        (
            self::$objects[$group] ?? [],
            $objects
        );
    }

    public static function getObjects (string $group)
    {
        return self::$objects[$group] ?? false;
    }

    public static function openEventEditor (int $component, string $method, string $form, VoidDesigner $designer = null)
    {
        $objects = self::getObjects ('editor');
        $editor  = $objects['Editor'];

        if (!isset (self::$events[$form]))
            self::$events[$form] = "class $form\n{\n\tpublic static function $method (WFObject \$self, \$args)\n\t{\n\t\t\n\t}\n}\n";

        elseif (!preg_match ('/function(\s)*'. $method .'(\s)*\(/i', self::$events[$form]))
            self::$events[$form] = ClassWorker::applyClass (self::$events[$form], $form, "\n\tpublic static function $method (WFObject \$self, \$args)\n\t{\n\t\t\n\t}\n");

        $editor->text = self::$events[$form];
        $editor->helpStorage = $form;

        // $form->caption = 'Событие "'. $event .'", объект "'. ($designer === null ? VoidEngine::getProperty ($component, 'Name') : $designer->getComponentName ($component)) .'"';

        $objects['MainForm']->showDialog ();
    }

    public static function stopProject ()
    {
        if (self::$project instanceof WFObject && !self::$project->hasExited)
        {
            self::$project->kill ();
            self::$project->waitForExit ();
        }
    }

    public static function startProject (TabControl $formsList, bool $debug = true): ?WFObject
    {
        self::stopProject ();

        try
        {
            VoidStudioBuilder::compileProject (VoidStudioProjectManager::$projectPath .'/build.exe', $formsList->items[0]->text, VoidStudioBuilder::getReferences (ENGINE_DIR .'/VoidEngine.php'), [], false, $debug);

            self::$project = run (VoidStudioProjectManager::$projectPath .'/build/build.exe');
        }

        catch (\Throwable $e)
        {
            messageBox ('Нельзя сохранить проект или произошла ошибка компиляции' ."\n\nПодробнее:\n\n". print_r ($e, true), 'Ошибка запуска проекта', enum ('System.Windows.Forms.MessageBoxButtons.OK'), enum ('System.Windows.Forms.MessageBoxIcon.Error'));

            return null;
        }

        return self::$project;
    }
}
