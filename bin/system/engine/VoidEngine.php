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
 * @version     2.1.3 build-2019/02/16 (major.minor.patch state-y/m/d)
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

namespace VoidEngine;

const ENGINE_DIR = __DIR__;
chdir (ENGINE_DIR);

$GLOBALS['__debug'] = [
    'start_time' => microtime (true)
];

$GLOBALS['__message_handler'] = function (...$args)
{
    throw new \Exception ($args);
};

require 'common/EngineInterfaces.php';
require 'common/Globals.php';
require 'common/Constants.php';
require 'common/Others.php';
require 'common/Events.php';

if (is_dir ('extensions'))
    foreach (scandir ('extensions') as $id => $ext)
        if (is_dir ('extensions/'. $ext) && file_exists ($ext = 'extensions/'. $ext .'/main.php'))
            require $ext;

require 'components/Component.php';
require 'components/Control.php';
require 'components/CommonDialog.php';
require 'components/FileDialog.php';
require 'components/ColorDialog.php';
require 'components/OpenFileDialog.php';
require 'components/SaveFileDialog.php';
require 'components/FolderBrowserDialog.php';
require 'components/Process.php';
require 'components/Timer.php';
require 'components/Image.php';
require 'components/PictureBox.php';
require 'components/MainMenu.php';
require 'components/MessageBox.php';
require 'components/Form.php';
require 'components/Label.php';
require 'components/TextBox.php';
require 'components/ProgressBar.php';
require 'components/PropertyGrid.php';
require 'components/Panel.php';
require 'components/Button.php';
require 'components/CheckBox.php';
require 'components/ComboBox.php';
require 'components/ListBox.php';
require 'components/ListView.php';
require 'components/TreeView.php';
require 'components/TabControl.php';
require 'components/WebBrowser.php';
require 'components/SplitContainer.php';
require 'components/FastColoredTextBox.php';
require 'components/Scintilla.php';
require 'components/Designer.php';

?>
