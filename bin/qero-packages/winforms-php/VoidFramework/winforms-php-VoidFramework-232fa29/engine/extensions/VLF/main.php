<?php

/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * @package     VLF (Void Language Format)
 * @copyright   2018 - 2019 Podvirnyy Nikita (KRypt0n_)
 * @license     GNU GPLv3 <https://www.gnu.org/licenses/gpl-3.0.html>
 * @license     Enfesto Studio Group license <https://vk.com/topic-113350174_36400959>
 * @author      Podvirnyy Nikita (KRypt0n_)
 * 
 * Contacts:
 *
 * Podvirnyy Nikita:
 * Email: <suimin.tu.mu.ga.mi@gmail.com>
 * VK:    vk.com/technomindlp
 *        vk.com/hphp_convertation
 * 
 * Формат файлов для разметки приложений на VoidEngine
 * 
 * Документация:
 * @see <https://vk.com/@winforms-vlf-dlya-chainikov>
 * 
 */

namespace VoidEngine;

const VLF_OBJECT_DEFINITION    = 1;
const VLF_PROPERTY_SET         = 2;
const VLF_METHOD_CALL          = 3;
const VLF_SUBOBJECT_DEFINITION = 4;
const VLF_OBJECT_REDIRECTION   = 5;
const VLF_RUNTIME_EXECUTABLE   = 6;

const VLF_EXT_DIR = __DIR__;

final class VLFLink
{
    public $name; // Имя объекта
    public $link; // АСД-ссылка на объект

    public function __construct (string $name, int $link)
    {
        $this->name = $name;
        $this->link = $link;
    }
}

require 'bin/parser.php';
require 'bin/interpreter.php';
require 'bin/importer.php';
