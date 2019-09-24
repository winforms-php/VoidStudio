<?php

namespace ConsoleArgs;

/**
 * Интерфейс всех параметров команд
 */
interface Parameter
{
    /**
     * Парсер значений
     */
    public function parse (array &$args);
}
