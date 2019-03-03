<?php

namespace VoidEngine;

class VoidEngine
{
    /**
     * * Создание объекта
     * 
     * @param ObjectType $object - объект конфигурации
     * [@param mixed ...$args = []] - список аргументов создания
     * 
     * @return int - возвращает указатель на созданный объект
     * 
     * VoidEngine::createObject (new ObjectType ('System.Windows.Forms.Button'));
     * 
     */

    public static function createObject (ObjectType $object, ...$args): int
    {
        return $object->extended ?
            winforms_createObject ($object->getResourceLine (), null, ...$args) :
            winforms_createObject (...$object->getResourceLine (), ...$args);
    }

    /**
     * * Удаление объектов
     * 
     * @param int ...$selectors - список указателей для удаления
     * 
     * $button_1 = VoidEngine::createObject (new ObjectType ('System.Windows.Forms.Button'));
     * $button_2 = VoidEngine::createObject (new ObjectType ('System.Windows.Forms.Button'));
     * 
     * VoidEngine::removeObjects ($button_1, $button_2);
     * 
     */

    public static function removeObjects (int ...$selectors): void
    {
        winforms_removeObjects (...$selectors);
    }

    /**
     * * Деструктор объекта
     * Удаляет указанный объект, если он больше не используеся в коде
     * 
     * @param int $selector - указатель на объект для удаления
     * 
     * $button = VoidEngine::createObject ('System.Windows.Forms.Button');
     * 
     * VoidEngine::destructObject ($button);
     * 
     */

    public static function destructObject (int $selector): bool
    {
        return winforms_destructObject ($selector);
    }

    /**
     * * Получение указателя на статичный класс
     * 
     * @param ObjectType $object - объект конфигурации класса
     * 
     * @return int - возвращает указатель на созданный объект
     * 
     * VoidEngine::createClass (new ObjectType ('System.Windows.Forms.MessageBox'));
     * 
     */

    public static function createClass (ObjectType $object): int
    {
        return $object->extended ?
            winforms_getClass ($object->getResourceLine (), null) :
            winforms_getClass (...$object->getResourceLine ());
    }

    /**
     * * Проверка объекта на существование
     * 
     * @param int $selector - указатель на проверяемый объект
     * 
     * @return bool - возвращает true, если объект существует, и false в противном случае
     * 
     * $button = VoidEngine::createObject (new ObjectType ('System.Windows.Forms.Button'));
     * VoidEngine::removeObjects ($button);
     * 
     * var_dump (VoidEngine::objectExists ($button)); // false
     * 
     */

    public static function objectExists (int $selector): bool
    {
        return winforms_objectExists ($selector);
    }

    /**
    * * Создание экземпляра типа объекта
    * 
    * @param mixed $object - объект конфигурации или полное название объекта
    * 
    * @return mixed - возвращает указатель на объект типа объекта или false в случае ошибки
    * 
    */

    public static function objectType ($object)
    {
        if ($object instanceof ObjectType)
            $object = $object->getResourceLine ();

        elseif (!is_string ($object))
            return false;

        return is_array ($object) ?
            winforms_typeof (...$object) :
            winforms_typeof ($object, null);
    }

    /**
     * * Получение свойства объекта
     * 
     * @param int $selector - указатель на объект
     * @param mixed $propertyName - название свойства
     * 
     * @param mixed $propertyName может быть передан с указанием на тип возвращаемого значения через структуру вида
     * [название свойства, возвращаемый им тип]
     * 
     * @return mixed - возвращает свойство объекта
     * 
     * $selector = VoidEngine::createObject (new ObjectType ('System.Windows.Forms.Button'));
     * 
     * pre (VoidEngine::getProperty ($selector, 'Text'));
     * pre (VoidEngine::getProperty ($selector, ['Text', 'string']));
     * 
     */

    public static function getProperty (int $selector, $propertyName)
    {
        return winforms_getProp ($selector, $propertyName);
    }

    /**
     * * Установка свойства объекта
     * 
     * @param int $selector - указатель на объект
     * @param string $propertyName - название свойства
     * @param mixed $value - значение свойства
     * 
     * @param mixed $value может быть передан в качестве определённого типа через структуру вида
     * [значение, тип]
     * 
     * $selector = VoidEngine::createObject (new ObjectType ('System.Windows.Forms.Button'));
     * 
     * VoidEngine::setProperty ($selector, 'Text', 'Hello!');
     * VoidEngine::setProperty ($selector, 'Text', ['Hello!', 'string']);
     * 
     */

    public static function setProperty (int $selector, string $propertyName, $value): void
    {
        winforms_setProp ($selector, $propertyName, $value);
    }

    /**
     * * Вызов метода объекта
     * 
     * @param int $selector - указатель на объект
     * @param mixed $methodName - название метода
     * [@param mixed ...$args = []] - аргументы вызова метода
     * 
     * @param mixed methodName так же может быть передан с указанием на тип возвращаемого методом значения через структуру вида
     * [название метода, возвращаемый им тип]
     * 
     * @return mixed - возвращает результат выполнения метода
     * 
     * $selector = VoidEngine::createClass (new ObjectType ('System.Windows.Forms.MessageBox'));
     * 
     * VoidEngine::callMethod ($selector, 'Show', 'Hello, World!', 'Test Box');
     * VoidEngine::callMethod ($selector, 'Show', ['Hello, World!', 'string'], ['Test Box', 'string']);
     * 
     * $result = VoidEngine::callMethod ($selector, ['Show', 'int'], ['Hello, World!', 'string'], ['Test Box', 'string']);
     * 
     */

    public static function callMethod (int $selector, $methodName, ...$args)
    {
        return winforms_callMethod ($selector, $methodName, ...$args);
    }

    /**
     * * Получение значения массива
     * 
     * @param int $selector - указатель на объект массива
     * @param mixed $index - индекс массива
     * 
     * @param mixed $index так же может быть передан с указанием на тип возвращаемого значения через структуру вида
     * [индекс, возвращаемый тип]
     * 
     * @return mixed - возвращает значение массива
     * 
     */

    public static function getArrayValue (int $selector, $index)
    {
        return winforms_getArrayValue ($selector, $index);
    }

    /**
     * * Установка значения массива
     * 
     * @param int $selector - указатель на объект массива
     * @param mixed $index - индекс массива
     * @param mixed $value - значение для установки
     * 
     * @param mixed $index может быть передан с указанием на его тип через структуру вида
     * [индекс, тип]
     * 
     * @param mixed value так же может быть передан с указанием на тип значения через структуру вида
     * [значение, тип]
     * 
     */

    public static function setArrayValue (int $selector, $index, $value): void
    {
        winforms_setArrayValue ($selector, $index, $value);
    }

    /**
     * * Установка события объекту
     * 
     * @param int $selector - указатель на объект
     * @param string $eventName - название события
     * [@param string $code = ''] - PHP код без тэгов
     * 
     * $selector = VoidEngine::createObject (new ObjectType ('System.Windows.Forms.Button'));
     * VoidEngine::setObjectEvent ($selector, 'Click', 'VoidEngine\pre (123);');
     * 
     */

    public static function setObjectEvent (int $selector, string $eventName, string $code = ''): void
    {
        if (self::eventExists ($selector, $eventName))
            self::removeObjectEvent ($selector, $eventName);

        winforms_setEvent ($selector, $eventName, $code);
        Components::setComponentEvent ($selector, $eventName, $code);
    }

    /**
     * * Проверка события объекта на существование
     * 
     * @param int $selector - указатель на объект
     * @param string $eventName - название события
     * 
     * @return bool - возвращает true в случае существования события
     * 
     * $selector = VoidEngine::createObject (new ObjectType ('System.Windows.Forms.Button'));
     * VoidEngine::setObjectEvent ($selector, 'Click', 'VoidEngine\pre (123);');
     * 
     * var_dump ($selector, 'Click'); // true
     * 
     */

    public static function eventExists (int $selector, string $eventName): bool
    {
        return winforms_eventExists ($selector, $eventName);
    }

    /**
     * * Удаление события объекта
     * 
     * @param int $selector - указатель на объект
     * @param string $eventName - название события
     * 
     * $selector = VoidEngine::createObject (new ObjectType ('System.Windows.Forms.Button'));
     * VoidEngine::setObjectEvent ($selector, 'Click', 'VoidEngine\pre (123);');
     * VoidEngine::removeObjectEvent ($selector, 'Click');
     * 
     * var_dump ($selector, 'Click'); // false
     * 
     */

    public static function removeObjectEvent (int $selector, string $eventName): void
    {
        winforms_removeEvent ($selector, $eventName);

        Components::removeComponentEvent ($selector, $eventName);
    }

    /**
     * * Импортирование объекта в ядро
     * 
     * @param string $data - сериализированные данные ядра
     * 
     * @return int - возвращает указатель на импортированный объект
     * 
     */

    public static function importObject (string $data): int
    {
        return winforms_importObject ($data);
    }

    /**
     * * Экспортирование объекта из ядра
     * 
     * @param int $selector - указатель на объект
     * 
     * @return string - возвращает сериализованные данные объекта
     * 
     */

    public static function exportObject (int $selector): string
    {
        return winforms_exportObject ($selector);
    }

    /**
     * * Компиляция PHP кода
     * 
     * @param string $savePath - путь для компиляции
     * @param string $iconPath - путь до иконки
     * @param string $phpCode - код для компиляции без тэгов
     * 
     * [@param string $productDescription = null] - описание приложения
     * [@param string $productName = null]        - название приложения
     * [@param string $productVersion = null]     - версия приложения
     * [@param string $companyName = null]        - компания-производителя
     * [@param string $copyright = null]          - копирайт
     * [@param string $callSharpCode = '']        - чистый C# код
     * [@param string $declareSharpCode = '']     - C# код с объявлениями классов
     * 
     */

    public static function compile (string $savePath, string $iconPath, string $phpCode, string $productDescription = null, string $productName = null, string $productVersion = null, string $companyName = null, string $copyright = null, string $callSharpCode = '', string $declareSharpCode = ''): array
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

        return winforms_compile ($savePath, $iconPath, $phpCode, $productDescription, $productName, $productVersion, $companyName, $copyright, $callSharpCode, $declareSharpCode);
    }
}

class EngineAdditions
{
    public static function loadModule (string $path): bool
    {
        try
        {
            (new WFClass ('System.Reflection.Assembly', 'mscorlib'))->loadFrom ($path);

            return true;
        }

        catch (\Throwable $e)
        {
            return false;
        }
    }

    public static function getProperty (int $selector, string $name): array
    {
        $property = VoidEngine::callMethod (VoidEngine::callMethod ($selector, 'GetType'), 'GetProperty', $name);

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

    public static function getObjectEvents (int $object): array
    {
        $events = [];

        $props = VoidEngine::callMethod (VoidEngine::callMethod ($object, 'GetType'), 'GetEvents');
        $len   = VoidEngine::getProperty ($props, 'Length');

        for ($i = 0; $i < $len; ++$i)
            $events[] = VoidEngine::getProperty (VoidEngine::getArrayValue ($props, $i), 'Name');

        return $events;
    }

    public static function coupleSelector ($value, int $selfSelector = null)
    {
        if (is_int ($value) && VoidEngine::objectExists ($value) && $value != $selfSelector)
            return VoidEngine::getProperty (VoidEngine::callMethod ($value, 'GetType'), 'IsArray') ?
                new Items ($value) : new WFObject ($value);

        else return $value;
    }

    public static function uncoupleSelector ($value)
    {
        return ($value instanceof WFObject || $value instanceof Items) ?
            $value->selector : $value;
    }
}

class Items extends \ArrayObject
{
    protected $selector;
	
	public function __construct (int $selector)
	{
		$this->selector = $selector;
    }
    
    public function __get ($name)
	{
		switch (strtolower ($name))
		{
            case 'count':
            case 'length':
            case 'size':
                try
                {
                    return VoidEngine::getProperty ($this->selector, 'Count');
                }

                catch (\WinFormsException $e)
                {
                    return VoidEngine::getProperty ($this->selector, 'Length');
                }
            break;
				
            case 'list':
                $size = $this->count;
                $list = [];
                
				for ($i = 0; $i < $size; ++$i)
                    $list[] = VoidEngine::getArrayValue ($this->selector, $i);
                     
                return $list;
            break;

            case 'names':
                $size = $this->count;
                $names = [];
                
                for ($i = 0; $i < $size; ++$i)
                    try
                    {
                        $names[] = VoidEngine::getProperty (VoidEngine::getArrayValue ($this->selector, [$i, 'object']), 'Text');
                    }

                    catch (\Throwable $e)
                    {
                        $names[] = VoidEngine::getArrayValue ($this->selector, [$i, 'string']);
                    }
                
                return $names;
            break;

            case 'selector':
                return $this->selector;
            break;
		}
    }
	
	public function add ($value)
	{
		return $this->offsetSet (null, $value);
	}
	
	public function append ($value)
	{
		return $this->offsetSet (null, $value);
	}
	
	public function offsetSet ($index, $value)
	{
        return VoidEngine::callMethod ($this->selector, $index === null ? 'Add' : 'Insert', $value instanceof WFObject ? $value->selector : $value);
	}
	
	public function offsetGet ($index)
	{
		return EngineAdditions::coupleSelector (VoidEngine::getArrayValue ($this->selector, $index), $this->selector);
	}
	
	public function addRange (array $items): void
	{
		array_map ([$this, 'append'], $items);
	}
	
	public function offsetUnset ($index): void
	{
		VoidEngine::callMethod ($this->selector, 'RemoveAt', $index);
	}
	
	public function remove ($index): void
	{
		$this->offsetUnset ($index);
	}
	
	public function clear (): void
	{
		VoidEngine::callMethod ($this->selector, 'Clear');
	}
	
	public function indexOf ($value): int
	{
		return VoidEngine::callMethod ($this->selector, 'IndexOf', $value instanceof WFObject ? $value->selector : $value);
	}
	
	public function insert ($index, $value)
	{
		return $this->offsetSet ($index, $value);
	}
	
	public function contains ($value): bool
	{
		return VoidEngine::callMethod ($this->selector, 'Contains', $value instanceof WFObject ? $value->selector : $value);
    }

    public function foreach (\Closure $callback, string $type = null)
    {
        $size = $this->count;

        for ($i = 0; $i < $size; ++$i)
            $callback ($i, EngineAdditions::coupleSelector (VoidEngine::getArrayValue ($this->selector, $type !== null ? [$i, $type] : $i), $this->selector));
    }
}

class ObjectType
{
    public $extended = false;

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

    public function getResourceLine ()
    {
        if ($this->extended)
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

        else return [$this->className, $this->classGroup];
    }
}

class WFObject
{
    protected $selector;

    public function __construct ($object, string $classGroup = null, bool $onlyClassInfo = false, ...$args)
    {
        if ($object instanceof ObjectType)
            $this->selector = VoidEngine::createObject ($object, ...$args);

        elseif (is_string ($object))
            $this->selector = VoidEngine::createObject (new ObjectType ($object, $classGroup, $onlyClassInfo), ...$args);

        elseif (is_int ($object) && VoidEngine::objectExists ($object))
            $this->selector = $object;

        else throw new \Exception ('$object parameter must be instance of "VoidEngine\ObjectType", be string or object selector');
    }
    
    public function __get ($name)
	{
        if (method_exists ($this, $method = "get_$name"))
            $value = $this->$method ();
            
        elseif (substr ($name, -5) == 'Event')
            $value = Events::getObjectEvent ($this->selector, substr ($name, 0, -5));

        elseif (property_exists ($this, $name))
            $value = $this->$name;
        
        else $value = $this->getProperty ($name);

        return EngineAdditions::coupleSelector ($value, $this->selector);
	}
	
	public function __set ($name, $value)
	{
        if (method_exists ($this, $method = "set_$name"))
            try
            {
                return $this->$method ($value);
            }

            # Метод "set_$name" может принимать в качестве параметра объект WFObject
            # т.к. наверняка мы не уверены, какой тип ему нужен, то тут требуется дополнительная проверка

            catch (\Throwable $e)
            {
                return ($value instanceof WFObject || $value instanceof Items) ?
                    $this->$method ($value->selector) : null;
            }

        elseif (substr ($name, -5) == 'Event')
            Events::setObjectEvent ($this->selector, substr ($name, 0, -5), $value);
        
        else $this->setProperty ($name, EngineAdditions::uncoupleSelector ($value));
	}
	
	public function __call ($method, $args)
	{
        $args = array_map (function ($arg)
        {
            return ($arg instanceof WFObject || $arg instanceof Items) ?
                $arg->selector : $arg;
        }, $args);

        return EngineAdditions::coupleSelector ($this->callMethod ($method, ...$args), $this->selector);
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
        
        VoidEngine::removeObjects ($array);
        
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

        elseif (is_int ($class) && VoidEngine::objectExists ($class))
            $this->selector = $class;

        else throw new \Exception ('$class parameter must be instance of "VoidEngine\ObjectType", be string or class selector');
    }
}

?>
