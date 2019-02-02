<?php

/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * @package     VoidEngine
 * @copyright   2018 - 2019 Podvirnyy Nikita (KRypt0n_) & Andrey Kusov
 * @license     GNU GPLv3 <https://www.gnu.org/licenses/gpl-3.0.html>
 * @license     Enfesto Studio Group license <https://vk.com/topic-113350174_36400959>
 * @see         license.txt for details
 * @author      Podvirnyy Nikita (KRypt0n_) & Andrey Kusov
 * 
 * @version     build-2019/02/02
 * 
 * Contacts:
 *
 * Podvirnyy Nikita:
 * Email: <suimin.tu.mu.ga.mi@gmail.com>
 * VK:    vk.com/technomindlp
 *        vk.com/hphp_convertation
 * 
 * Andrey Kusov:
 * VK: vk.com/postmessagea
 * 
 */

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

if (is_array ($files = scandir ('debug')))
    foreach ($files as $id => $file)
        if (is_file ('debug/'. $file))
            unlink ('debug/'. $file);

require 'common/WFLinks.php';
require 'common/Others.php';
require 'common/EngineInterface.php';
require 'common/Globals.php';
require 'common/Constants.php';
// require 'common/LanguageFiles.php'; // Признан устаревшим

if (is_array ($exts = scandir ('extensions')))
    foreach ($exts as $id => $ext)
        if (is_dir ('extensions/'. $ext) && file_exists ($ext = 'extensions/'. $ext .'/main.php'))
            require $ext;

require 'events/Events.php';
require 'events/MouseEventArgs.php';
require 'events/CancelEventArgs.php';
require 'events/FormClosedEventArgs.php';
require 'events/FormClosingEventArgs.php';
require 'events/KeyEventArgs.php';
require 'events/KeyPressEventArgs.php';
require 'events/PropertyValueChangedEventArgs.php';

require 'components/Component.php';
require 'components/Control.php';
require 'components/MessageBox.php';
require 'components/Image.php';
require 'components/Process.php';
require 'components/Timer.php';
require 'components/ScrollBar.php';
require 'components/SplitContainer.php';
require 'components/ContextMenuStrip.php';
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
require 'components/VideoBox.php';
require 'components/CheckBox.php';
require 'components/WebBrowser.php';
require 'components/FastColoredTextBox.php';
require 'components/Scintilla.php';
require 'components/TabControl.php';
require 'components/ListBox.php';
require 'components/PropertyGrid.php';
require 'components/CommonDialog.php';
require 'components/FileDialog.php';
require 'components/OpenFileDialog.php';
require 'components/SaveFileDialog.php';
require 'components/ColorDialog.php';
require 'components/FolderBrowserDialog.php';
require 'components/Designer.php';
require 'components/MainMenu.php';

$app = dirname (dirname (ENGINE_DIR)) .'/app/start.php';

if (file_exists ($app))
    require $app;

?>
