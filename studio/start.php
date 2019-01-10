<?php

namespace VoidEngine;

const STUDIO_DIR = __DIR__;
chdir (STUDIO_DIR);

require __DIR__ .'/VoidStudio API.php';
require __DIR__ .'/forms/main.php'; // Главная форма среды
require __DIR__ .'/forms/editor.php'; // Редактор событий компонентов

$APPLICATION->run (VoidStudioAPI::getObjects ('main')['MainForm']);

?>
