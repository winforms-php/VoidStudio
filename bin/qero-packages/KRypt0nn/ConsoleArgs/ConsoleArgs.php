<?php

/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * @package     ConsoleArgs
 * @copyright   2019 Podvirnyy Nikita (KRypt0n_)
 * @license     GNU GPLv3 <https://www.gnu.org/licenses/gpl-3.0.html>
 * @license     Enfesto Studio Group license <https://vk.com/topic-113350174_36400959>
 * @author      Podvirnyy Nikita (KRypt0n_)
 * 
 * Contacts:
 *
 * Email: <suimin.tu.mu.ga.mi@gmail.com>
 * VK:    vk.com/technomindlp
 *        vk.com/hphp_convertation
 * 
 */

namespace ConsoleArgs;

/**
 * Объект локализаций
 * Вы можете создать объект, указать в нём свои данные локализации и использовать его в командах, менеджере и т.п.
 */
class Locale
{
    public $execution_error            = '$callable must be any closure';
    public $command_type_exception     = '$command must be ConsoleArgs\Command object or instance of him';
    public $command_undefined_error    = 'You should write any available command';
    public $unselected_value_exception = 'You should write param value';
    public $param_type_exception       = '$param must be instance of ConsoleArgs\\Parameter interface';
    public $undefined_param_exception  = 'You must define this param';
    public $aliase_exists_exception    = 'This aliase already exists';
}

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
            throw new \Exception ($this->locale->undefined_param_exception);

        return $this->defaultValue;
    }
}

/**
 * Объект сеттеров
 * Отвечает за объявление сет-параметров команд
 */
class Setter implements Parameter
{
    public $names;
    public $separator;
    public $defaultValue;
    public $required;
    protected $locale;

    /**
     * Конструктор
     * 
     * @param string $name - имя сеттера
     * [@param string $separator = '='] - разделитель сеттера и значения
     * [@param string $defaultValue = null] - значение сеттера по умолчанию
     * [@param bool $required = false] - обязательно ли указание сеттера
     */
    public function __construct (string $name, string $separator = '=', string $defaultValue = null, bool $required = false)
    {
        $this->names        = [$name];
        $this->separator    = $separator;
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
        $l    = strlen ($this->separator);

        foreach ($this->names as $name)
            foreach ($args as $id => $arg)
                if (substr ($arg, 0, ($pos = strlen ($name) + $l)) == $name . $this->separator)
                {
                    $param = [substr ($arg, $pos)];

                    unset ($args[$id]);
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
            throw new \Exception ($this->locale->undefined_param_exception);

        return $this->defaultValue;
    }
}

/**
 * Объект команд
 * Отвечает за выполнение команд и работу с параметрами
 */
class Command
{
    public $name;
    public $callable;
    public $params = [];

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

/**
 * Менеджер команд
 * Предоставляет возможность работы с командами через аргументы консоли
 */
class Manager
{
    public $commands = [];
    public $defaultCommand = null;
    protected $locale;

    /**
     * Конструктор
     * 
     * @param array $commands - список команд
     * [@param DefaultCommand $defaultCommand = null] - объект дефолтной команды
     */
    public function __construct (array $commands, DefaultCommand $defaultCommand = null)
    {
        $this->locale = new Locale;
        $this->defaultCommand = $defaultCommand;

        foreach ($commands as $command)
            if ($command instanceof Command)
                $this->commands[$command->name] = $command;

            else throw new \Exception ($this->locale->command_type_exception);
    }

    /**
     * Установка локализации
     * 
     * @param Locale $locale - объект локализации
     * 
     * @return Manager - возвращает сам себя
     */
    public function setLocale (Locale $locale): Manager
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * Установка дефолтной команды
     * 
     * @param DefaultCommand $defaultCommand - объект дефолтной команды
     * 
     * @return Manager - возвращает сам себя
     */
    public function setDefault (DefaultCommand $defaultCommand): Manager
    {
        $this->defaultCommand = $defaultCommand;

        return $this;
    }

    /**
     * Итерация выполнения по аргументам
     * 
     * @param array $args - список аргументов консоли
     */
    public function execute (array $args)
    {
        $args = array_values ($args);

        if (!isset ($args[0]))
        {
            if ($this->defaultCommand !== null)
                return $this->defaultCommand->execute ($args);

            else throw new \Exception ($this->locale->command_undefined_error);
        }

        $name = $args[0];
        $args = array_slice ($args, 1);

        if (!isset ($this->commands[$name]))
            return $this->defaultCommand !== null ?
                $this->defaultCommand->execute ($args) : false;

        return $this->commands[$name]->execute ($args);
    }
}
