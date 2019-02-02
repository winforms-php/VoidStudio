<?php

/*
    Класс-интерфейс VoidEngine'а
    Отвечает за работу с WinForms-ядром со стороны PHP (.NET Framework)
*/

namespace VoidEngine;

class VoidEngine
{
    /**
     * * Создание объекта
     * 
     * @param WFObject object - объект конфигурации
     * [@param args - список аргументов создания]
     * 
     * @return int selector - возвращает указатель на созданный объект
     * 
     * VoidEngine::createObject (new WFObject ('System.Windows.Forms.Button'));
     * 
     */

    static function createObject (WFObject $object, ...$args): int
    {
        return winforms_objectcreate ($object->getResourceLine (), ...$args);
    }

    /**
     * * Удаление объекта
     * 
     * @param int selectors - список указателей для удаления
     * 
     * $button_1 = VoidEngine::createObject (new WFObject ('System.Windows.Forms.Button'));
     * $button_2 = VoidEngine::createObject (new WFObject ('System.Windows.Forms.Button'));
     * 
     * VoidEngine::removeObject ($button_1, $button_2);
     * 
     */

    static function removeObject (int ...$selectors): void
    {
        winforms_objectdelete (...$selectors);
    }

    /**
     * * Получение указателя на статичный класс
     * 
     * @param WFObject object - объект конфигурации класса
     * 
     * @return int selector - возвращает указатель на созданный объект
     * 
     * VoidEngine::buildObject (new WFObject ('System.Windows.Forms.MessageBox'));
     * 
     */

    static function buildObject (WFObject $object): int
    {
        return winforms_objectget ($object->getResourceLine ());
    }

    /**
     * * Проверка объекта на существование
     * 
     * @param int selector - указатель на проверяемый объект
     * 
     * @return bool exists - возвращает true, если объект существует, и false в противном случае
     * 
     * $button = VoidEngine::createObject (new WFObject ('System.Windows.Forms.Button'));
     * VoidEngine::removeObject ($button);
     * 
     * var_dump (VoidEngine::objectExists ($button)); // false
     * 
     */

    static function objectExists (int $selector): bool
    {
        return winforms_objectexists ($selector);
    }

    /**
     * * Создание экземпляра типа объекта
     * 
     * @param object - объект конфигурации или полное название объекта
     * 
     * @return mixed type - возвращает указатель на объект типа объекта или false в случае ошибки
     * 
     */

    static function objectType ($object)
    {
        if ($object instanceof WFObject)
            $object = $object->getResourceLine ();

        elseif (!is_string ($object))
            return false;

        return winforms_typeof ($object);
    }

    /**
     * * Получение свойства объекта
     * 
     * @param int selector - указатель на объект
     * @param propertyName - название свойства
     * 
     * @param propertyName может быть передан с указанием на тип возвращаемого значения через структуру вида
     * [название свойства, возвращаемый им тип]
     * 
     * @return property - возвращает свойство объекта
     * 
     * $selector = VoidEngine::createObject (new WFObject ('System.Windows.Forms.Button'));
     * 
     * pre (VoidEngine::getProperty ($selector, 'Text'));
     * pre (VoidEngine::getProperty ($selector, ['Text', 'string']));
     * 
     */

    static function getProperty (int $selector, $propertyName)
    {
        return winforms_getprop ($selector, $propertyName);
    }

    /**
     * * Установка свойства объекта
     * 
     * @param int selector - указатель на объект
     * @param string propertyName - название свойства
     * @param value - значение свойства
     * 
     * @param value может быть передан в качестве определённого типа через структуру вида
     * [значение, тип]
     * 
     * $selector = VoidEngine::createObject (new WFObject ('System.Windows.Forms.Button'));
     * 
     * VoidEngine::setProperty ($selector, 'Text', 'Hello!');
     * VoidEngine::setProperty ($selector, 'Text', ['Hello!', 'string']);
     * 
     */

    static function setProperty (int $selector, string $propertyName, $value): void
    {
        winforms_setprop ($selector, $propertyName, $value);
    }

    /**
     * * Вызов метода объекта
     * 
     * @param int selector - указатель на объект
     * @param methodName - название метода
     * 
     * @param methodName так же может быть передан с указанием на тип возвращаемого методом значения через структуру вида
     * [название метода, возвращаемый им тип]
     * 
     * @return result - возвращает результат выполнения метода
     * 
     * $selector = VoidEngine::buildObject (new WFObject ('System.Windows.Forms.MessageBox'));
     * 
     * VoidEngine::callMethod ($selector, 'Show', 'Hello, World!', 'Test Box');
     * VoidEngine::callMethod ($selector, 'Show', ['Hello, World!', 'string'], ['Test Box', 'string']);
     * 
     * $result = VoidEngine::callMethod ($selector, ['Show', 'int'], ['Hello, World!', 'string'], ['Test Box', 'string']);
     * 
     */

    static function callMethod (int $selector, $methodName, ...$args)
    {
        return winforms_callmethod ($selector, $methodName, ...$args);
    }

    /**
     * * Получение значения массива
     * 
     * @param int selector - указатель на объект массива
     * @param index - индекс массива
     * 
     * @param index так же может быть передан с указанием на тип возвращаемого значения через структуру вида
     * [индекс, возвращаемый тип]
     * 
     * @return value - возвращает значение массива
     * 
     */

    static function getArrayValue (int $selector, $index)
    {
        return winforms_getindex ($selector, $index);
    }

    /**
     * * Установка значения массива
     * 
     * @param int selector - указатель на объект массива
     * @param index - индекс массива
     * @param value - значение для установки
     * 
     * @param indexможет быть передан с указанием на его тип через структуру вида
     * [индекс, тип]
     * 
     * @param value так же может быть передан с указанием на тип значения через структуру вида
     * [значение, тип]
     * 
     */

    static function setArrayValue (int $selector, $index, $value): void
    {
        winforms_setindex ($selector, $index, $value);
    }

    /**
     * * Установка события объекту
     * 
     * @param int selector - указатель на объект
     * @param string eventName - название события
     * @param string code - PHP код без тэгов
     * 
     * $selector = VoidEngine::createObject (new WFObject ('System.Windows.Forms.Button'));
     * VoidEngine::setObjectEvent ($selector, 'Click', 'VoidEngine\pre (123);');
     * 
     */

    static function setObjectEvent (int $selector, string $eventName, string $code = ''): void
    {
        if (self::eventExists ($selector, $eventName))
            self::removeEvent ($selector, $eventName);

        try
        {
            winforms_setevent ($selector, $eventName, $code);

            Components::setComponentEvent ($selector, $eventName, $code);
        }

        catch (\Throwable $e)
        {
            throw $e;
        }
    }

    /**
     * * Проверка события объекта на существование
     * 
     * @param int selector - указатель на объект
     * @param string eventName - название события
     * 
     * $selector = VoidEngine::createObject (new WFObject ('System.Windows.Forms.Button'));
     * VoidEngine::setObjectEvent ($selector, 'Click', 'VoidEngine\pre (123);');
     * 
     * var_dump ($selector, 'Click'); // true
     * 
     */

    static function eventExists (int $selector, string $eventName): bool
    {
        return winforms_existsevent ($selector, $eventName);
    }

    /**
     * * Удаление события объекта
     * 
     * @param int selector - указатель на объект
     * @param string eventName - название события
     * 
     * $selector = VoidEngine::createObject (new WFObject ('System.Windows.Forms.Button'));
     * VoidEngine::setObjectEvent ($selector, 'Click', 'VoidEngine\pre (123);');
     * VoidEngine::removeEvent ($selector, 'Click');
     * 
     * var_dump ($selector, 'Click'); // false
     * 
     */

    static function removeEvent (int $selector, string $eventName): void
    {
        winforms_delevent ($selector, $eventName);

        Components::removeComponentEvent ($selector, $eventName);
    }

    /**
     * * Импортирование объекта в ядро
     * 
     * @param string data - сериализированные данные ядра
     * 
     * @return int selector - возвращает указатель на импортированный объект
     * 
     */

    static function importObject (string $data): int
    {
        return winforms_dataimport ($data);
    }

    /**
     * * Экспортирование объекта из ядра
     * 
     * @param int selector - указатель на объект
     * 
     * @return string data - возвращает сериализованные данные объекта
     * 
     */

    static function exportObject (int $selector): string
    {
        return winforms_dataexport ($selector);
    }

    /**
     * * Компиляция PHP кода
     * 
     * @param string savePath - путь для компиляции
     * @param string iconPath - путь до иконки
     * @param string phpCode - код для компиляции без тэгов
     * 
     */

    static function compile (string $savePath, string $iconPath, string $phpCode): void
    {
        winforms_compile ($savePath, $iconPath, $phpCode);
    }
}

class EngineAdditions
{
    static function loadModule (string $path): void
    {
        $assembly = new WFClass ('System.Reflection.Assembly', 'mscorlib');
        $assembly->LoadFrom ($path);
    }

    static function getObjectProperties (int $selector, bool $extended = false): array
    {
        $properties = [];

        $type  = VoidEngine::callMethod ($selector, 'GetType');
        $props = VoidEngine::callMethod ($type, 'GetProperties');
        $len   = VoidEngine::getProperty ($props, 'Length');

        for ($i = 0; $i < $len; ++$i)
        {
            $index = VoidEngine::getArrayValue ($props, $i);
            $name  = VoidEngine::getProperty ($index, 'Name');

            $property = self::getProperty ($selector, $name);

            $properties[$name] = $extended ?
                $property : $property['value'];
        }

        return $properties;
    }

    static function getProperty (int $selector, string $name): array
    {
        $type     = VoidEngine::callMethod ($selector, 'GetType');
        $property = VoidEngine::callMethod ($type, 'GetProperty', $name);

        try
        {
            $propertyType = VoidEngine::getProperty ($property, 'PropertyType');

            switch ($propertyType)
            {
                case 'System.String':
                    $property = 'string';
                break;

                case 'System.Int32':
                case 'System.Int64':
                    $property = 'int';
                break;

                case 'System.Double':
                    $property = 'double';
                break;

                case 'System.Single':
                    $property = 'float';
                break;

                case 'System.Boolean':
                    $property = 'bool';
                break;

                case 'System.IntPtr':
                    $property = 'handle';
                break;

                case 'System.Drawing.Color':
                    $property = 'color';
                break;

                default:
                    $property = 'int';
                break;
            }
        }

        catch (\WinFormsException $e)
        {
            $property = 'object';
        }

        return [
            'type'  => $property,
            'value' => VoidEngine::getProperty ($selector, [$name, $property])
        ];
    }
}

class WFObject
{
    public $version  = '4.0.0.0';
    public $culture  = 'neutral';
    public $token    = 'b77a5c561934e089';
    public $postArgs = [];

    public $className;
    public $classGroup;

    public $onlyClassInfo;

    public function __construct (string $className, string $classGroup = 'System.Windows.Forms', bool $onlyClassInfo = false)
    {
        $this->className     = $className; // System.Windows.Forms.Application
        $this->classGroup    = $classGroup; // System.Windows.Forms
        $this->onlyClassInfo = $onlyClassInfo;
    }

    public function getResourceLine (): string
    {
        if ($this->onlyClassInfo)
            return ($this->classGroup) ?
                $this->className .', '. $this->classGroup :
                $this->className;

        $postArgs = '';

        if (isset ($this->postArgs) && is_array ($this->postArgs))
            foreach ($this->postArgs as $name => $value)    
                $postArgs .= ", $name=$value";

        return $this->className .', '. $this->classGroup .', Version='. $this->version .', Culture='. $this->culture .', PublicKeyToken='. $this->token .$postArgs;
    }
}

class WFClass
{
    protected $class;

    public function __construct ($class, string $classGroup = 'System.Windows.Forms', bool $onlyClassInfo = false)
    {
        if ($class instanceof WFObject)
            $this->class = VoidEngine::buildObject ($class);

        elseif (is_string ($class))
            $this->class = VoidEngine::buildObject (new WFObject ($class, $classGroup, $onlyClassInfo));

        else throw new \Exception ("\"$class\" parameter must be instance of \"VoidEngine\\WFObject\" or be string");
    }

    public function __get ($name)
    {
        if (is_int ($this->class))
            return VoidEngine::getProperty ($this->class, $name);

        else throw new \Exception ('Class isn\'t initialized');
    }

    public function __set ($name, $value)
    {
        if (is_int ($this->class))
            VoidEngine::setProperty ($this->class, $name, $value);

        else throw new \Exception ('Class isn\'t initialized');
    }

    public function __call ($method, $args)
	{
        if (is_int ($this->class))
            return VoidEngine::callMethod ($this->class, $method, ...$args);

        else throw new \Exception ('Class isn\'t initialized');
	}
}

?>
