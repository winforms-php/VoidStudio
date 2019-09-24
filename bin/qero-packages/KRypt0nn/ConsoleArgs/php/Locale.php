<?php

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
    public $undefined_param_exception  = 'You must define param %param_name%';
    public $aliase_exists_exception    = 'This aliase already exists';
}
