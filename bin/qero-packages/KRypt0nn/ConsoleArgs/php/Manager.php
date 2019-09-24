<?php

namespace ConsoleArgs;

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
        {
            foreach ($this->commands as $command)
                if (in_array ($name, $command->aliases))
                    return $command->execute ($args);

            return $this->defaultCommand !== null ?
                $this->defaultCommand->execute ($args) : false;
        }

        return $this->commands[$name]->execute ($args);
    }
}
