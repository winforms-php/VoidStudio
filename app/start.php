<?php

namespace VoidEngine;

const APP_DIR = __DIR__;
chdir (APP_DIR);

require 'VoidStudio API.php';
require 'forms/main.php'; // Главная форма среды
// require 'forms/editor.php'; // Редактор событий компонентов
require 'forms/build.php'; // Билдер проектов

$APPLICATION->run (VoidStudioAPI::getObjects ('main')['MainForm']);

?>
