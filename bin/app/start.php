<?php

namespace VoidEngine;

# Объявление констант
const APP_DIR  = __DIR__;

$package = json_decode (@file_get_contents (dirname (__DIR__) .'/qero-packages/packages.json'), true);

define ('VoidEngine\CORE_DIR', isset ($package['github:winforms-php/VoidFramework']['basefolder']) ?
	dirname (__DIR__) .'/qero-packages/winforms-php/VoidFramework/'. $package['github:winforms-php/VoidFramework']['basefolder'] .'/core' : __DIR__);

# Подгружаем PHP расширения
foreach (glob (CORE_DIR .'/ext/php_*.dll') as $ext)
	if (!extension_loaded (substr (basename ($ext), 4, -4)))
		load_extension ($ext);

# Подгружаем Qero-пакеты
require __DIR__ .'/../qero-packages/autoload.php';

chdir (APP_DIR); // Меняем стандартную директорию на директорию приложения

# Подгружаем скрипты VoidStudio
require 'VoidStudio API.php';
require 'forms/main.php'; // Главная форма среды
require 'forms/editor.php'; // Редактор событий компонентов
require 'forms/modules.php'; // Менеджер модулей проекта
require 'forms/addPackage.php'; // Диалог добавления Qero-пакета в модули
require 'forms/viewPackage.php'; // Диалог обзора Qero-пакета
require 'forms/build.php'; // Билдер проектов
require 'forms/diagnostic.php'; // Средство диагностики
require 'forms/about.php'; // О программе

$APPLICATION->run (VoidStudioAPI::getObjects ('main')['MainForm']);
