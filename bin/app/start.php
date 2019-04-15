<?php

namespace VoidEngine;

const APP_DIR = __DIR__;
chdir (APP_DIR);

/*

Оп! Нельзя начинать использовать пока не появится возможности делать несколько потоков

$form = new Form;
$form->backColor = clWhite;
$form->formBorderStyle = fbsNone;
$form->startPosition = fspCenterScreen;
$form->topMost = true;
$form->doubleBuffered = true;
$form->size = [512, 256];

$logo = new PictureBox ($form);
$logo->imageLocation = text (APP_DIR .'/system/icons/Icon.png');
$logo->sizeMode = smStretchImage;
$logo->bounds = [32, 32, 142, 128];

$caption = new Label ($form);
$caption->location = [208, 32];
$caption->font = ['Segoe UI Light', 26];
$caption->caption = 'VoidStudio';
$caption->autoSize = true;

$version = new Label ($form);
$version->location = [208, 78];
$version->font = ['Segoe UI Light', 12];
$version->caption = ENGINE_VERSION;
$version->autoSize = true;

$status = new Label ($form);
$status->location = [28, 212];
$status->font = ['Segoe UI Light', 10];
$status->caption = text ('Запуск среды...');
$status->autoSize = true;

$form->show ();

*/

require 'VoidStudio API.php';
require 'forms/main.php'; // Главная форма среды
require 'forms/editor.php'; // Редактор событий компонентов
require 'forms/modules.php'; // Менеджер модулей проекта
require 'forms/build.php'; // Билдер проектов
require 'forms/diagnostic.php'; // Средство диагностики
require 'forms/about.php'; // О программе

$APPLICATION->run (VoidStudioAPI::getObjects ('main')['MainForm']);
