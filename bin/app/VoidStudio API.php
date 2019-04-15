<?php

namespace VoidEngine;

// require 'Qero/Qero.phar';

if (!file_exists (dirname (APP_DIR) .'/VoidStudio.lnk'))
{
    $link = (new \COM ('WScript.Shell'))->CreateShortcut (text (dirname (APP_DIR) .'/VoidStudio.lnk'));
    $link->TargetPath = text (CORE_DIR .'/WinForms PHP.exe');
    $link->WorkingDirectory = text (CORE_DIR);
    $link->Save ();
}

try
{
    if (strpos ((new WFObject ('System.Net.WebClient'))->downloadString ('https://raw.githubusercontent.com/KRypt0nn/WinForms-PHP/master/bin/app/system/blacklist.lst'), sha1 (shell_exec ('wmic csproduct'))) !== false)
        messageBox (text ('Ваш компьютер добавлен в чёрный список проекта WinForms PHP. Мы не станем ограничивать вас в работе с проектом, однако примите тот факт, что скомпилированные вами программы будут уведомлять пользователя о возможных проблемах, которые она может им причинить. Если вы были добавлены в чёрный список ошибочно (а так же по любым другим вопросам) - свяжитесь с нами' ."\n\nС уважением, команда разработчиков проекта WinForms PHP\nvk.com/winforms"), text ('Предупреждение'), enum ('System.Windows.Forms.MessageBoxButtons.OK'), enum ('System.Windows.Forms.MessageBoxIcon.Warning'));
}

catch (\Throwable $e) {}

if (date ('m/d') == '08/18')
    messageBox (text ("Привет, друзья!\nСегодня знаменательный день: день рождения проекта WinForms PHP!\nС момента его появления прошло уже ". (date ('Y') - 2018) ." лет!\n\nВот такие дела. Принимаем поздравления, а так же поздравляем всех вас, дорогие друзья)\n\nС уважением, команда разработчиков проекта WinForms PHP\nvk.com/winforms"), text ('Уведомление'), enum ('System.Windows.Forms.MessageBoxButtons.OK'), enum ('System.Windows.Forms.MessageBoxIcon.Information'));

class VoidStudioAPI
{
    public static $objects = [];
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
            messageBox (text ('Нельзя сохранить проект или произошла ошибка компиляции' ."\n\nПодробнее:\n\n". print_r ($e, true)), text ('Ошибка запуска проекта'), enum ('System.Windows.Forms.MessageBoxButtons.OK'), enum ('System.Windows.Forms.MessageBoxIcon.Error'));

            return null;
        }

        return self::$project;
    }
}

class VoidStudioDebugger
{
    public $process;
    protected $lastTimestamp = 0;

    public function __construct (WFObject $process)
    {
        if ($process->getType ()->toString () == 'System.Diagnostics.Process')
            $this->process = $process;

        else throw new \Exception ('$process argument must be an "Process" object');
    }

    public function dump (string $savePath, string $properties = '', bool $waitForExit = true)
    {
        $process = run (text ('"'. APP_DIR .'/system/procdump/procdump.exe"'), text ($properties .' '. $this->process->id .' "'. filepathNoExt ($savePath) .'"'));

        if ($waitForExit)
            while (!$process->hasExited)
                usleep (200);
    }

    public function debugRequest (string $command, array $arguments = [])
    {
        file_put_contents (text (VoidStudioProjectManager::$projectPath .'/build/__debug_request'), json_encode ([
            'timestamp' => time (),
            'command'   => $command,
            'arguments' => $arguments
        ], JSON_PRETTY_PRINT));
    }

    public function readDebugAnswer (bool $wait = false)
    {
        $file = text (VoidStudioProjectManager::$projectPath .'/build/__debug_answer');

        if ($wait)
            while (!file_exists ($file))
                usleep (100);

        if (file_exists ($file))
        {
            $answer = json_decode (file_get_contents ($file), true);
            unlink ($file);

            if ($answer['timestamp'] > $this->lastTimestamp)
            {
                $this->lastTimestamp = $answer['timestamp'];

                return $answer['data'];
            }
        }

        return false;
    }
}

class VoidStudioProjectManager
{
    public static $projectPath = '';

    public static function createProject (string $name = 'default'): bool
    {
        if (!is_dir ($dir = 'C:/Users/'. USERNAME .'/Documents/VoidStudio'))
            dir_create ($dir);

        if (is_dir ($dir = $dir .'/'. $name))
        {
            if (messageBox (text ('Проект "'. $name .'" уже создан. Перезаписать его?'), text ('Подтвердите действие'), enum ('System.Windows.Forms.MessageBoxButtons.YesNo'), enum ('System.Windows.Forms.MessageBoxIcon.Question')) == 6)
                dir_clean ($dir);

            else return false;
        }

        else dir_create ($dir);

        self::$projectPath = $dir;

        VoidStudioAPI::getObjects ('main')['MainForm']->caption = $name .' - VoidStudio';
        VoidStudioAPI::getObjects ('main')['ToolsPanel__LogList']->items->add (text ('Проект "'. $name .'" успешно создан'));

        dir_create ($dir .'/modules');

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

            $info['forms'][$item] = (string) VoidStudioBuilder::appendDesignerData ($designer->getSharpCode ($item, true), $designer);
            $info['objects'][$item] = array_keys ($designer->objects);

            foreach ($designer->objects as $name => $objectType)
                if (isset (Components::$events[$designer->getComponentByName ($name)]) && sizeof (Components::$events[$designer->getComponentByName ($name)]) > 0)
                    $info['events'][$item][$name] = Components::$events[$designer->getComponentByName ($name)];
        }

        file_put_contents ($file, gzdeflate (serialize ($info), 9));

        if (replaceSl (self::$projectPath) != replaceSl (dirname ($file)))
            dir_copy (self::$projectPath .'/modules', dirname ($file) .'/modules');

        VoidStudioAPI::getObjects ('main')['ToolsPanel__LogList']->items->add (text ('Проект успешно сохранён'));
    }

    public static function openProject (string $file): void
    {
        messageBox (text ('В настоящий момент пересматривается алгоритм открытия проектов, поэтому возможны многочисленные баги (в т.ч. невозможность компиляции открытого проекта)'. "\n\nБудет исправлено в ближайшее время\nС уважением, разработчики проекта WinForms PHP"), text ('Предупреждение об ошибках'), enum ('System.Windows.Forms.MessageBoxButtons.OK'), enum ('System.Windows.Forms.MessageBoxIcon.Warning'));

        self::$projectPath = dirname ($file); 

        $info    = unserialize (gzinflate (file_get_contents ($file)));
        $objects = VoidStudioAPI::getObjects ('main');

        $objects['PropertiesList__List']->selectedObject = null;
        $objects['Designer__FormsList']->items->foreach (function ($value)
        {
            try
            {
                $designer = VoidStudioAPI::$objects['main']['Designer__'. $value->text .'Designer'];

                $designer->form->dispose ();
                $designer->control->dispose ();

                unset ($designer, VoidStudioAPI::$objects['main']['Designer__'. $value->text .'Designer']);
            }

            catch (\Throwable $e) {}
        });

        $objects['Designer__FormsList']->items->clear ();

        foreach ($info['forms'] as $formName => $form)
        {
            $sourceForm = (new WFClass ('WinForms_PHP.WFCompiler', ''))->evalCS ("using System;\nusing System.Windows.Forms;\nusing System.Reflection;\nusing System.Linq;\nusing System.ComponentModel;\n\npublic class CodeEvaler\n{\n\tpublic object EvalCode ()\n\t{\n\t\tForm form = new $formName ();\n\n\t\tVoidControlsParser.parseControlsForOpening (\"$formName\", (Control) form);\n\n\t\treturn form;\n\t}\n}\n\n". file_get_contents (APP_DIR .'/system/presets/compile_parser_preset.cs') ."\n\n". (string)($form), true);

            $form = $GLOBALS['__underConstruction'][$formName];
            unset ($GLOBALS['__underConstruction'], $form[$formName]);

            $formObjects = array_flip ($info['objects'][$formName]);

            foreach ($form as $name => $selector)
                if (!isset ($formObjects[$name]))
                    unset ($form[$name]);

            $page = new TabPage ($formName);
            $page->backgroundColor = clWhite;

            $designer = new VoidDesigner ($page, $formName, $objects['PropertiesList__List'], $objects['EventsList__ActiveEvents'], $objects['PropertiesPanel__SelectedComponent'], $objects['Designer__FormsList'], $sourceForm);
            $designer->initDesigner ();

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

            if (isset ($info['events'][$formName][$formName]))
                foreach ($info['events'][$formName][$formName] as $eventName => $event)
                {
                    Events::reserveObjectEvent ($designer->form->selector, $eventName);
                    VoidEngine::setObjectEvent ($designer->form->selector, $eventName, $event);
                }

            $objects['Designer__FormsList']->items->add ($page);
        }

        // Components::cleanJunk ();

        $objects['Designer__FormsList']->selectedTab = $page;

        $objects['PropertiesPanel__SelectedComponent']->items->clear ();
        $objects['PropertiesPanel__SelectedComponent']->items->addRange (array_keys ($designer->objects));
        $objects['PropertiesPanel__SelectedComponent']->selectedItem = $formName;

        VoidStudioAPI::getObjects ('main')['MainForm']->caption = basenameNoExt ($file) .' - VoidStudio';

        $objects['PropertiesList__List']->selectedObject = $designer->form;
        $designer->focus ();
    }
}

class VoidStudioBuilder
{
    public static function compileProject (string $save, string $enteringPoint, array $references, array $settings = [], bool $printSuccessCompile = false, bool $debug = false): array
    {
        $savePath   = text (dirname ($save) .'/'. basenameNoExt ($save));
        $strClass   = (new WFClass ('System.String', 'mscorlib'))->selector;
        $globalCode = new WFObject (VoidEngine::callMethod ($strClass, ['Concat', 'object'], file_get_contents (APP_DIR .'/system/presets/compile_parser_preset.cs') ."\n\n"));
        $forms      = [];

        for ($i = 0; $i < 5; ++$i)
            if (!isset ($settings[$i]) || !strlen (trim ($settings[$i])))
                $settings[$i] = null;

        $settings = array_slice ($settings, 0, 5);

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
                            
                            $eventStr = '        '. $name .'.'. $eventName .' += (sender, e) => WinForms_PHP.ZendProgram.CallEvent (WinForms_PHP.ZendProgram.HashByObject ('. $name .'), @"namespace VoidEngine; $self = _c($self); $args = isset ($args) && is_int ($args) && VoidEngine::objectExists ($args) ? new EventArgs ($args) : null; " + WinForms_PHP.ZendProgram.getResource ("'. VoidEngine::exportObject ($eventStr) .'"), e);';

                            $globalCode = new WFObject (VoidEngine::callMethod ($globalCode->selector, ['Replace', 'object'], $str, substr ($str, 0, $pos). $eventStr .substr ($str, $pos)));
                        }
                    }
        }

        dir_clean ($savePath);
        dir_copy (CORE_DIR, $savePath);
        
        unlink ($savePath .'/script.php');
        unlink ($savePath .'/WinForms PHP.exe');

        $errors = VoidEngine::compile ($savePath .text ('/'. basename ($save)), text (APP_DIR .'/system/icons/Icon.ico'), str_replace_assoc (file_get_contents (APP_DIR .'/system/presets/compile_main_preset.php'), [
            '%VoidEngine%' => VoidStudioBuilder::generateCode ($references),

            '%modules%' => (file_exists ($modulesFile = VoidStudioProjectManager::$projectPath .'/modules/Qero.json') && sizeof ($modules = json_decode (file_get_contents ($modulesFile))) ? "require 'qero-packages/autoload.php';\n\n" : "\n\n"). implode ("\n", array_map (function ($module)
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
        $code   = new WFObject ($code);
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

            $code = new WFObject (VoidEngine::callMethod ($code->selector, ['Replace', 'object'], substr ($code, $pos + 2, $end - $pos), 'WinForms_PHP.ZendProgram.getResource ("'. VoidEngine::exportObject ($object) .'")'));
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
            }, file ($path)), 1));

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
