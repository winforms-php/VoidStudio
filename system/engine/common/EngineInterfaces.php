<?php

namespace VoidEngine;

class VoidEngine
{
    /**
     * * Создание объекта
     * 
     * @param object - объект конфигурации
     * [@param args - список аргументов создания]
     * 
     * @return selector - возвращает указатель на созданный объект
     * 
     * VoidEngine::createObject (new ObjectType ('System.Windows.Forms.Button'));
     * 
     */

    static function createObject (ObjectType $object, ...$args): int
    {
        return winforms_objectcreate ($object->getResourceLine (), ...$args);
    }

    /**
     * * Удаление объекта
     * 
     * @param selectors - список указателей для удаления
     * 
     * $button_1 = VoidEngine::createObject (new ObjectType ('System.Windows.Forms.Button'));
     * $button_2 = VoidEngine::createObject (new ObjectType ('System.Windows.Forms.Button'));
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
     * @param object - объект конфигурации класса
     * 
     * @return selector - возвращает указатель на созданный объект
     * 
     * VoidEngine::createClass (new ObjectType ('System.Windows.Forms.MessageBox'));
     * 
     */

    static function createClass (ObjectType $object): int
    {
        return winforms_objectget ($object->getResourceLine ());
    }

    /**
     * * Проверка объекта на существование
     * 
     * @param selector - указатель на проверяемый объект
     * 
     * @return exists - возвращает true, если объект существует, и false в противном случае
     * 
     * $button = VoidEngine::createObject (new ObjectType ('System.Windows.Forms.Button'));
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
     * @return type - возвращает указатель на объект типа объекта или false в случае ошибки
     * 
     */

    static function objectType ($object)
    {
        if ($object instanceof ObjectType)
            $object = $object->getResourceLine ();

        elseif (!is_string ($object))
            return false;

        return winforms_typeof ($object);
    }

    /**
     * * Получение свойства объекта
     * 
     * @param selector - указатель на объект
     * @param propertyName - название свойства
     * 
     * @param propertyName может быть передан с указанием на тип возвращаемого значения через структуру вида
     * [название свойства, возвращаемый им тип]
     * 
     * @return property - возвращает свойство объекта
     * 
     * $selector = VoidEngine::createObject (new ObjectType ('System.Windows.Forms.Button'));
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
     * @param selector - указатель на объект
     * @param propertyName - название свойства
     * @param value - значение свойства
     * 
     * @param value может быть передан в качестве определённого типа через структуру вида
     * [значение, тип]
     * 
     * $selector = VoidEngine::createObject (new ObjectType ('System.Windows.Forms.Button'));
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
     * @param selector - указатель на объект
     * @param methodName - название метода
     * 
     * @param methodName так же может быть передан с указанием на тип возвращаемого методом значения через структуру вида
     * [название метода, возвращаемый им тип]
     * 
     * @return result - возвращает результат выполнения метода
     * 
     * $selector = VoidEngine::createClass (new ObjectType ('System.Windows.Forms.MessageBox'));
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
     * @param selector - указатель на объект массива
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
     * @param selector - указатель на объект массива
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
     * @param selector - указатель на объект
     * @param eventName - название события
     * @param code - PHP код без тэгов
     * 
     * $selector = VoidEngine::createObject (new ObjectType ('System.Windows.Forms.Button'));
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
     * @param selector - указатель на объект
     * @param eventName - название события
     * 
     * $selector = VoidEngine::createObject (new ObjectType ('System.Windows.Forms.Button'));
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
     * @param selector - указатель на объект
     * @param eventName - название события
     * 
     * $selector = VoidEngine::createObject (new ObjectType ('System.Windows.Forms.Button'));
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
     * @param data - сериализированные данные ядра
     * 
     * @return selector - возвращает указатель на импортированный объект
     * 
     */

    static function importObject (string $data): int
    {
        return winforms_dataimport (gzinflate (base64_decode ($data)));
    }

    /**
     * * Экспортирование объекта из ядра
     * 
     * @param selector - указатель на объект
     * 
     * @return data - возвращает сериализованные данные объекта
     * 
     */

    static function exportObject (int $selector): string
    {
        return base64_encode (gzdeflate (winforms_dataexport ($selector), 9));
    }

    /**
     * * Компиляция PHP кода
     * 
     * @param savePath - путь для компиляции
     * @param iconPath - путь до иконки
     * @param phpCode - код для компиляции без тэгов
     * 
     * [@param productDescription = ''] - описание приложения
     * [@param productName = '']        - название приложения
     * [@param productVersion = '']     - версия приложения
     * [@param companyName = '']        - компания-производителя
     * [@param copyright = '']          - копирайт
     * 
     */

    static function compile (string $savePath, string $iconPath, string $phpCode, string $productDescription = null, string $productName = null, string $productVersion = null, string $companyName = null, string $copyright = null): void
    {
        if ($productName === null)
            $productName = basenameNoExt ($savePath);

        if ($productDescription === null)
            $productDescription = $productName;

        if ($productVersion === null)
            $productVersion = '1.0';

        if ($companyName === null)
            $companyName = 'Company N';

        if ($copyright === null)
            $copyright = $companyName .' copyright (c) '. date ('Y');

        winforms_compile ($savePath, $iconPath, $phpCode, $productDescription, $productName, $productVersion, $companyName, $copyright);
    }
}

class EngineAdditions
{
    static function loadModule (string $path): void
    {
        $assembly = new WFClass ('System.Reflection.Assembly', 'mscorlib');
        $assembly->loadFrom ($path);
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

    static function getProperty (int $selector, string $name)
    {
        $type     = VoidEngine::callMethod ($selector, 'GetType');
        $property = VoidEngine::callMethod ($type, 'GetProperty', $name);

        if (!is_int ($property))
            return false;

        try
        {
            $propertyType = VoidEngine::getProperty ($property, ['PropertyType', 'string']);

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
                    try
                    {
                        VoidEngine::getProperty ($selector, [$name, 'int']);

                        $property = 'int';
                    }

                    catch (\WinFormsException $e)
                    {
                        return [
                            'type'  => 'vrsf',
                            'value' => VoidEngine::exportObject (VoidEngine::getProperty ($selector, [$name, 'object']))
                        ];
                    }
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

    static function getObjectEvents (int $object)
    {
        $events = [];

        $type  = VoidEngine::callMethod ($object, 'GetType');
        $props = VoidEngine::callMethod ($type, 'GetEvents');
        $len   = VoidEngine::getProperty ($props, 'Length');

        for ($i = 0; $i < $len; ++$i)
        {
            $index = VoidEngine::getArrayValue ($props, $i);
            $name  = VoidEngine::getProperty ($index, 'Name');

            $events[] = $name;
        }

        return $events;
    }
}

class ObjectType
{
    public $version  = '4.0.0.0';
    public $culture  = 'neutral';
    public $token    = 'b77a5c561934e089';
    public $postArgs = [];

    public $className;
    public $classGroup;

    public $onlyClassInfo;

    public function __construct (string $className, string $classGroup = null, bool $onlyClassInfo = false)
    {
        $this->className     = $className;
        $this->classGroup    = $classGroup;
        $this->onlyClassInfo = $onlyClassInfo;

        if ($this->classGroup === null)
            $this->classGroup = substr ($this->className, 0, strrpos ($this->className, '.'));
    }

    public function getResourceLine (): string
    {
        if ($this->onlyClassInfo)
            return $this->classGroup ?
                $this->className .', '. $this->classGroup :
                $this->className;

        $postArgs = '';
        $line     = $this->className;

        if (isset ($this->postArgs) && is_array ($this->postArgs))
            foreach ($this->postArgs as $name => $value)    
                $postArgs .= ", $name=$value";

        if ($this->classGroup)
            $line .= ', '. $this->classGroup;

        return $line .', Version='. $this->version .', Culture='. $this->culture .', PublicKeyToken='. $this->token .$postArgs;
    }
}

class WFObject
{
    protected $selector;

    public function __construct ($object, string $classGroup = null, bool $onlyClassInfo = false)
    {
        if ($object instanceof ObjectType)
            $this->selector = VoidEngine::createObject ($object);

        elseif (is_string ($object))
            $this->selector = VoidEngine::createObject (new ObjectType ($object, $classGroup, $onlyClassInfo));

        elseif (is_int ($object) && VoidEngine::objectExists ($object))
            $this->selector = $object;

        else throw new \Exception ('$object parameter must be instance of "VoidEngine\ObjectType" or be string');
    }
    
    public function __get ($name)
	{
        if (method_exists ($this, $method = "get_$name"))
            $value = $this->$method ();
            
        elseif (substr ($name, strlen ($name) - 5) == 'Event')
            $value = Events::getObjectEvent ($this->selector, substr ($name, 0, -5));

        elseif (property_exists ($this, $name))
            $value = $this->$name;
        
        else $value = $this->getProperty ($name);

        if (is_int ($value) && VoidEngine::objectExists ($value) && $value != $this->selector)
            $value = new WFObject ($value);

        return $value;
	}
	
	public function __set ($name, $value)
	{
        if (method_exists ($this, $method = "set_$name"))
            try
            {
                return $this->$method ($value);
            }

            catch (\Throwable $e)
            {
                return $value instanceof WFObject ?
                    $this->$method ($value->selector) : null;
            }

        elseif (substr ($name, strlen ($name) - 5) == 'Event')
            Events::setObjectEvent ($this->selector, substr ($name, 0, -5), $value);
        
        else try
        {
            $this->setProperty ($name, $value);
        }

        catch (\Throwable $e)
        {
            $value instanceof WFObject ?
                $this->setProperty ($name, $value->selector) :
                $this->setProperty ($name, (string) $value);
        }
	}
	
	public function __call ($method, $args)
	{
        $value = $this->callMethod ($method, ...$args);

        if (is_int ($value) && VoidEngine::objectExists ($value))
            $value = new WFObject ($value);

        return $value;
	}
	
    protected function getProperty ($name)
    {
        return VoidEngine::getProperty ($this->selector, $name);
    }

    protected function setProperty ($name, $value)
    {
        VoidEngine::setProperty ($this->selector, $name, $value);
    }
	
    protected function callMethod ($method, ...$args)
    {
        return VoidEngine::callMethod ($this->selector, $method, ...$args);
    }
	
	protected function getArrayProperty ($name, string $type = null)
	{
        $array  = $this->getProperty ($name);
        $size   = VoidEngine::getProperty ($array, 'Length');
        $return = [];

		for ($i = 0; $i < $size; ++$i)
            $return[] = VoidEngine::getArrayValue ($array, $type === null ? $i : [$i, $type]);
        
        VoidEngine::removeObject ($array);
        
		return $return;
	}
}

class WFClass extends WFObject
{
    protected $selector;

    public function __construct ($class, string $classGroup = null, bool $onlyClassInfo = false)
    {
        if ($class instanceof ObjectType)
            $this->selector = VoidEngine::createClass ($class);

        elseif (is_string ($class))
            $this->selector = VoidEngine::createClass (new ObjectType ($class, $classGroup, $onlyClassInfo));

        else throw new \Exception ('$class parameter must be instance of "VoidEngine\ObjectType" or be string');
    }
}

?>
