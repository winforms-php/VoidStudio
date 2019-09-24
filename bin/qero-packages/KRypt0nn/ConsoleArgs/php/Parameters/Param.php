<?php

namespace ConsoleArgs;

/**
 * Объект параметров
 * Отвечает за объявление параметров команд
 */
class Param implements Parameter
{
    public $names;
    public $defaultValue;
    public $required;
    protected $locale;

    /**
     * Конструктор
     * 
     * @param string $name - имя парамтера
     * [@param string $defaultValue = null] - значение параметра по умолчанию
     * [@param bool $required = false] - обязательно ли указание параметра
     */
    public function __construct (string $name, string $defaultValue = null, bool $required = false)
    {
        $this->names        = [$name];
        $this->defaultValue = $defaultValue;
        $this->required     = $required;

        $this->locale = new Locale;
    }

    /**
     * Установка локализации
     * 
     * @param Locale $locale - объект локализации
     * 
     * @return Param - возвращает сам себя
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
     * @return Param - возвращает сам себя
     */
    public function addAliase (string $name)
    {
        if (array_search ($name, $this->names) !== false)
            throw new \Exception ($this->locale->aliase_exists_exception);

        $this->names[] = $name;

        return $this;
    }

    /**
     * Парсер параметров
     * 
     * @param array &$args - массив аргументов для парсинга
     * 
     * Возвращает найденый параметр или массив найдёных параметров, если их было указано несколько
     */
    public function parse (array &$args)
    {
        $args = array_values ($args);

        foreach ($this->names as $name)
            if (($key = array_search ($name, $args)) !== false)
            {
                if (!isset ($args[$key + 1]))
                    throw new \Exception ($this->locale->unselected_value_exception);

                $param = [$args[$key + 1]];

                unset ($args[$key], $args[$key + 1]);
                $args = array_values ($args);

                try
                {
                    while (($altParam = $this->parse ($args)) !== $this->defaultValue)
                    {
                        if (is_array ($altParam))
                            $param = array_merge ($param, $altParam);

                        else $param[] = $altParam;
                    }
                }

                catch (\Throwable $e) {}
                
                return sizeof ($param) == 1 ?
                    $param[0] : $param;
            }

        if ($this->required)
            throw new \Exception (str_replace ('%param_name%', current ($this->names), $this->locale->undefined_param_exception));

        return $this->defaultValue;
    }
}
