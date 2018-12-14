<?php

namespace VoidEngine;

const WORKSPACE = __DIR__;

require __DIR__ .'/VoidStudio API.php';
require __DIR__ .'/forms/load.php';
require __DIR__ .'/forms/main.php';

VoidStudioAPI::getObjects ('load')['MainForm']->showDialog ();
VoidStudioAPI::getObjects ('main')['MainForm']->showDialog ();

?>
