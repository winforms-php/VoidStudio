<?php

namespace ConsoleArgs;

/**
 * Объект флагов
 * Отвечает за создание флагов для команд
 */
class Flag implements Parameter
{
    public $names;
    protected $locale;

    /**
     * Конструктор
     * 
     * @param string $name - имя флага
     */
    public function __construct (string $name)
    {
        $this->names = [$name];
    }

    /**
     * Установка локализации
     * 
     * @param Locale $locale - объект локализации
     * 
     * @return Flag - возвращает сам себя
     */
    public function setLocale (Locale $locale): Param
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * Добавление алиаса
     * 
     * @param string $name - алиас для добавления
     * 
     * @return Flag - возвращает сам себя
     */
    public function addAliase (string $name)
    {
        if (array_search ($name, $this->names) !== false)
            throw new \Exception ($this->locale->aliase_exists_exception);

        $this->names[] = $name;

        return $this;
    }

    /**
     * Парсер флагов
     * 
     * @param array &$args - массив аргументов для парсинга
     * 
     * Возвращает состояние флага
     */
    public function parse (array &$args)
    {
        $args = array_values ($args);

        foreach ($this->names as $name)
            if (($key = array_search ($name, $args)) !== false)
            {
                unset ($args[$key]);
                $args = array_values ($args);

                while ($this->parse ($args) !== false);
                
                return true;
            }

        return false;
    }
}
