<?php

namespace VoidEngine;

/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * @package     VoidStudio
 * @copyright   2018 - 2019 Podvirnyy Nikita (KRypt0n_) & Andrey Kusov
 * @license     GNU GPLv3 <https://www.gnu.org/licenses/gpl-3.0.html>
 * @license     Enfesto Studio Group license <https://vk.com/topic-113350174_36400959>
 * @see         license.txt for details
 * @author      Podvirnyy Nikita (KRypt0n_) & Andrey Kusov
 * 
 * @version     2.1.0 (major.minor.patch)
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
 * ! Отредачил версию среды? Измени файл qero-info.json
 * 
 */

const STUDIO_VERSION = '2.1.0';

require 'php/PropertyGrid.php';
require 'php/EventGrid.php';
require 'php/Designer.php';
require 'php/VoidStudio API.php';
require 'php/ProjectManager.php';
require 'php/Debugger.php';
require 'php/Builder.php';
require 'php/ClassWorker.php';

/*if (!file_exists (dirname (APP_DIR, 2) .'/VoidStudio.lnk'))
{
    $link = (new \COM ('WScript.Shell'))->CreateShortcut (dirname (APP_DIR, 2) .'/VoidStudio.lnk');
    $link->TargetPath = CORE_DIR .'/VoidCore.exe';
    $link->Arguments  = APP_DIR .'/start.php';
    $link->WorkingDirectory = CORE_DIR;
    $link->Save ();
}*/

try
{
    if (strpos ((new WFObject ('System.Net.WebClient'))->downloadString ('https://raw.githubusercontent.com/winforms-php/VoidStudio/master/bin/app/system/blacklist.lst'), sha1 (shell_exec ('wmic csproduct'))) !== false)
        messageBox ('Ваш компьютер добавлен в чёрный список проекта WinForms PHP. Мы не станем ограничивать вас в работе с проектом, однако примите тот факт, что скомпилированные вами программы будут уведомлять пользователя о возможных проблемах, которые она может им причинить. Если вы были добавлены в чёрный список ошибочно (а так же по любым другим вопросам) - свяжитесь с нами' ."\n\nС уважением, команда разработчиков проекта WinForms PHP\nvk.com/winforms", 'Предупреждение', enum ('System.Windows.Forms.MessageBoxButtons.OK'), enum ('System.Windows.Forms.MessageBoxIcon.Warning'));
}

catch (\Throwable $e) {}

if (date ('m/d') == '06/18')
    messageBox ("Привет, друзья!\nСегодня знаменательный день: день рождения проекта WinForms PHP!\nС момента его появления прошло уже ". (date ('Y') - 2018) ." лет!\n\nВот такие дела. Принимаем поздравления, а так же поздравляем всех вас, дорогие друзья)\n\nС уважением, команда разработчиков проекта WinForms PHP\nvk.com/winforms", 'Уведомление', enum ('System.Windows.Forms.MessageBoxButtons.OK'), enum ('System.Windows.Forms.MessageBoxIcon.Information'));
