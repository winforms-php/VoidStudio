<?php

/*
    Класс-линковщик, который является главным классом всего движка VoidEngine
    Так же подключает вспомогательные классы для работы с GUI
*/

namespace VoidEngine;

const ENGINE_DIR = __DIR__;

chdir (ENGINE_DIR);

$GLOBALS['__debug'] = [
    'start_time'  => microtime (true),
    'error_count' => 0
];

$files = scandir ('debug');

if (is_array ($files))
    foreach ($files as $id => $file)
        if (is_file ('debug/'. $file))
            unlink ('debug/'. $file);

require 'common/WFLinks.php';
require 'common/Others.php';
require 'common/EngineInterface.php';
require 'common/Globals.php';
require 'common/Constants.php';
require 'common/LanguageFiles.php';

require 'events/Events.php';
require 'events/MouseEventArgs.php';
require 'events/CancelEventArgs.php';
require 'events/FormClosedEventArgs.php';
require 'events/FormClosingEventArgs.php';
require 'events/KeyEventArgs.php';
require 'events/KeyPressEventArgs.php';

require 'components/Component.php';
require 'components/Control.php';
require 'components/MessageBox.php';
require 'components/Process.php';
require 'components/Timer.php';
require 'components/ScrollBar.php';
require 'components/Panel.php';
require 'components/PictureBox.php';
require 'components/Form.php';
require 'components/Button.php';
require 'components/Label.php';
require 'components/TextBox.php';
require 'components/ProgressBar.php';
require 'components/ListView.php';
require 'components/TreeView.php';
require 'components/ComboBox.php';
require 'components/PropertyGrid.php';
require 'components/VideoBox.php';
require 'components/CheckBox.php';
require 'components/WebBrowser.php';
require 'components/FastColoredTextBox.php';
require 'components/Scintilla.php';
require 'components/TabControl.php';
require 'components/ListBox.php';
require 'components/CommonDialog.php';
require 'components/FileDialog.php';
require 'components/OpenFileDialog.php';
require 'components/SaveFileDialog.php';
require 'components/ColorDialog.php';
require 'components/FolderBrowserDialog.php';
require 'components/Designer.php';
require 'components/MainMenu.php';

class Components
{
    static $components = [];
    static $events = [];

    static function addComponent (int $selector, object $object)
    {
        self::$components[$selector] = $object;
        self::$events[$selector] = [];
    }

    static function getComponent (int $selector)
    {
        return isset (self::$components[$selector]) ?
            self::$components[$selector] : false;
    }

    static function setComponentEvent (int $selector, string $eventName, string $code)
    {
        self::$events[$selector][$eventName] = $code;
    }

    static function getComponentEvent (int $selector, string $eventName)
    {
        return isset (self::$events[$selector][$eventName]) ?
            self::$events[$selector][$eventName] : false;
    }

    static function removeComponentEvent (int $selector, string $eventName)
    {
        unset (self::$events[$selector][$eventName]);
    }

    static function removeComponent (int $selector)
    {
        unset (self::$components[$selector], self::$events[$selector]);
    }
}

class Clipboard
{
    static $clipboard;

    public static function getText ()
    {
        if (!isset (self::$clipboard))
            self::$clipboard = VoidEngine::buildObject (new WFObject ('System.Windows.Forms.Clipboard'));

        return VoidEngine::callMethod (self::$clipboard, 'GetText', 'string');
    }
    
    public static function setText (string $text)
    {
        if (!isset (self::$clipboard))
            self::$clipboard = VoidEngine::buildObject (new WFObject ('System.Windows.Forms.Clipboard'));

        VoidEngine::callMethod (self::$clipboard, 'SetText', '', $text, 'string');
    }
    
    public static function getFiles ()
    {
        if (!isset (self::$clipboard))
            self::$clipboard = VoidEngine::buildObject (new WFObject ('System.Windows.Forms.Clipboard'));

        $array = VoidEngine::callMethod (self::$clipboard, 'GetFileDropList', 'object');
        $size  = VoidEngine::getProperty ($arr, 'Count', 'int');

        for ($i = 0; $i < $size; ++$i)
            $files[] = VoidEngine::getArrayValue ($arr, $i, 'string');

        VoidEngine::removeObject ($array);

        return $files;
    }
    
    public static function setFiles (array $files)
    {
        if (!isset (self::$clipboard))
            self::$clipboard = VoidEngine::buildObject (new WFObject ('System.Windows.Forms.Clipboard'));

        $coll = VoidEngine::buildObject (new WFObject ('System.Collections.Specialized.StringCollection', 'System'));

        foreach($files as $file)
            VoidEngine::callMethod ($coll, 'Add', '', (string) $file, 'string');

        VoidEngine::callMethod (self::$clipboard, 'SetFileDropList', '', $coll, 'object');
        VoidEngine::removeObject ($coll);
    }
}

/*

WFCompiler::compile ('test.exe', 'C:\Users\KRypt0n_\Desktop\VoidStudio\studio\Icon.ico', '<?php

winforms_callmethod (winforms_objectcreate(\'System.Windows.Forms.Form, System.Windows.Forms, Version=4.0.0.0, Culture=neutral, PublicKeyToken=b77a5c561934e089\'), \'ShowDialog\');

?>');

*/

$studioStart = dirname (dirname (ENGINE_DIR)) .'/studio/start.php';

if (file_exists ($studioStart))
    require $studioStart;

?>
