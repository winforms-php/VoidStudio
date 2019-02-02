<?php

namespace VoidEngine;

const STUDIO_DIR = __DIR__;
chdir (STUDIO_DIR);

require 'VoidStudio API.php';
require 'forms/main.php'; // Главная форма среды
// require 'forms/editor.php'; // Редактор событий компонентов
// require 'forms/builder.php'; // Билдер проектов

$APPLICATION->run (VoidStudioAPI::getObjects ('main')['MainForm']);

?>
