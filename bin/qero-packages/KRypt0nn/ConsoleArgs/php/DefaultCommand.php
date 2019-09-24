<?php

namespace ConsoleArgs;

/**
 * Объект дефолтной команды
 * Выполняется если менеджеру была передана некорректная команда
 */
class DefaultCommand extends Command
{
    /**
     * Конструктор
     * 
     * [@param \Closure $callable = null] - анонимная функция для выполнения
     */
    public function __construct (\Closure $callable = null)
    {
        if ($callable !== null)
            $this->callable = $callable;
    }
}
