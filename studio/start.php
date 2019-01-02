<?php

namespace VoidEngine;

const WORKSPACE = __DIR__;

require __DIR__ .'/VoidStudio API.php';
require __DIR__ .'/forms/main.php'; // Главная форма среды
require __DIR__ .'/forms/editor.php'; // Редактор событий компонентов

VoidStudioAPI::getObjects ('main')['MainForm']->showDialog ();

?>
