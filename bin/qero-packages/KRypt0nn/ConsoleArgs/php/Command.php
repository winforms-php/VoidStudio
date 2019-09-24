<?php

namespace ConsoleArgs;

/**
 * Объект команд
 * Отвечает за выполнение команд и работу с параметрами
 */
class Command
{
    public $name;
    public $callable;
    public $params  = [];
    public $aliases = [];

    protected $locale;

    /**
     * Конструктор
     * 
     * @param string $name - имя команды
     * [@param \Closure $callable = null] - анонимная функция для выполнения
     */
    public function __construct (string $name, \Closure $callable = null)
    {
        $this->name   = $name;
        $this->locale = new Locale;

        if ($callable !== null)
            $this->callable = $callable;
    }

    /**
     * Установка локализации
     * 
     * @param Locale $locale - объект локализации
     * 
     * @return Command - возвращает сам себя
     */
    public function setLocale (Locale $locale): Command
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * Установка параметров
     * 
     * @param array $params - список параметров для установки
     * 
     * @return Command - возвращает сам себя
     */
    public function addParams (array $params): Command
    {
        foreach ($params as $param)
            if ($param instanceof Parameter)
                $this->params[current ($param->names)] = $param;

            else throw new \Exception ($this->locale->param_type_exception);

        return $this;
    }

    /**
     * Добавление алиаса
     * 
     * @param string $name - алиас для добавления
     * 
     * @return Command - возвращает сам себя
     */
    public function addAliase (string $name)
    {
        if (array_search ($name, $this->aliases) !== false)
            throw new \Exception ($this->locale->aliase_exists_exception);

        $this->aliases[] = $name;

        return $this;
    }

    /**
     * Парсинг параметров
     * 
     * @param array &$args - аргументы для парсинга
     * 
     * @return array - возвращает ассоциативный массив [параметр] => [значение]
     */
    public function getParams (array &$args): array
    {
        $params = array_combine (array_keys ($this->params), array_fill (0, sizeof ($this->params), null));

        foreach ($this->params as $name => $param)
            $params[$name] = $param->parse ($args);

        return $params;
    }

    /**
     * Выполнение команды
     * 
     * @param array &$args - аргументы команды
     */
    public function execute (array &$args)
    {
        if ($this->callable instanceof \Closure)
        {
            $params = $this->getParams ($args);

            return $this->callable->call ($this, array_values ($args), $params);
        }

        throw new \Exception ($this->locale->execution_error);
    }
}
