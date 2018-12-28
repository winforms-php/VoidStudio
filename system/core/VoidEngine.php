<?php

/*
    Класс-линковщик, который является главным классом всего движка VoidEngine
    Так же подключает вспомогательные классы для работы с GUI
*/

namespace VoidEngine;

const ENGINE_DIR = __DIR__;

$basePath = __DIR__;
chdir ($basePath);

require "$basePath/common/WFLinks.php";
require "$basePath/common/Others.php";

$GLOBALS['__debug'] = [
    'start_time'  => microtime (true),
    'error_count' => 0
];

$GLOBALS['__message_handler'] = function ($error) {throw new \Exception ($error);}; // \VoidEngine\pre

$files = scandir ("$basePath/debug");

if (is_array ($files))
    foreach ($files as $id => $file)
        if (is_file ("$basePath/debug/$file"))
            unlink ("$basePath/debug/$file");

require "$basePath/common/EngineInterface.php";
require "$basePath/common/Constants.php";
require "$basePath/common/Globals.php";
require "$basePath/common/LanguageFiles.php";

require "$basePath/events/Events.php";
require "$basePath/events/MouseEventArgs.php";
require "$basePath/events/CancelEventArgs.php";
require "$basePath/events/FormClosedEventArgs.php";
require "$basePath/events/FormClosingEventArgs.php";
require "$basePath/events/KeyEventArgs.php";
require "$basePath/events/KeyPressEventArgs.php";

require "$basePath/components/Component.php";
require "$basePath/components/Control.php";
require "$basePath/components/MessageBox.php";
require "$basePath/components/Process.php";
require "$basePath/components/Timer.php";
require "$basePath/components/ScrollBar.php";
require "$basePath/components/Panel.php";
require "$basePath/components/PictureBox.php";
require "$basePath/components/Form.php";
require "$basePath/components/Button.php";
require "$basePath/components/Label.php";
require "$basePath/components/TextBox.php";
require "$basePath/components/ListView.php";
require "$basePath/components/TreeView.php";
require "$basePath/components/ComboBox.php";
require "$basePath/components/PropertyGrid.php";
require "$basePath/components/VideoBox.php";
require "$basePath/components/CheckBox.php";
require "$basePath/components/WebBrowser.php";
require "$basePath/components/FastColoredTextBox.php";
require "$basePath/components/Scintilla.php";
require "$basePath/components/TabControl.php";
require "$basePath/components/ListBox.php";
require "$basePath/components/CommonDialog.php";
require "$basePath/components/FileDialog.php";
require "$basePath/components/OpenFileDialog.php";
require "$basePath/components/SaveFileDialog.php";
require "$basePath/components/ColorDialog.php";
require "$basePath/components/FolderBrowserDialog.php";
require "$basePath/components/ObjectsCount.php";

class Components
{
    static $components = [];

    static function addComponent (string $selector, object $object)
    {
        self::$components[$selector] = $object;
    }

    static function getComponent (string $selector)
    {
        return (
            isset (self::$components[$selector]) ?
            self::$components[$selector] : false
        );
    }

    static function removeComponent (string $selector)
    {
        unset (self::$components[$selector]);
    }
}

/*class EdgedArray
{
	private $array = [];
	private $arrayCount;
	
	public function __construct (int $count = 3)
	{
		$this->arrayCount = $count;
	}
	
	public function set ($value)
	{
		if (sizeof ($this->array) >= $this->arrayCount)
            array_shift ($this->array);
            
		$this->array[] = $value;
	}
	
	public function get (int $index = 0)
	{
		return $this->array[$index];
	}
	
	public function splay ()
	{
		return $this->array;
	}
}*/

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

$studioStart = dirname (dirname ($basePath)) .'/studio/start.php';

if (file_exists ($studioStart))
    require $studioStart;

?>
