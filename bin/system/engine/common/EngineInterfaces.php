<?php

namespace VoidEngine;

class VoidEngine
{
    /**
     * * Создание объекта
     * 
     * @param mixed $objectName - полное название объекта
     * [@param mixed $objectGroup = null] - полное пространство имён объекта
     * [@param mixed ...$args = []] - список аргументов создания
     * 
     * @return int - возвращает указатель на созданный объект
     * 
     * VoidEngine::createObject ('System.Windows.Forms.Button', 'System.Windows.Forms');
     * 
     */

    public static function createObject ($objectName, $objectGroup = null, ...$args): int
    {
        return winforms_createObject ($objectName, $objectGroup, ...$args);
    }

    /**
     * * Удаление объектов
     * 
     * @param int ...$selectors - список указателей для удаления
     * 
     * $button_1 = VoidEngine::createObject ('System.Windows.Forms.Button', 'System.Windows.Forms');
     * $button_2 = VoidEngine::createObject ('System.Windows.Forms.Button', 'System.Windows.Forms');
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
     * @param mixed $className - полное название класса
     * [@param mixed $classGroup = null] - полное пространство имён класса
     * 
     * @return int - возвращает указатель на созданный объект
     * 
     * VoidEngine::createClass ('System.Windows.Forms.MessageBox');
     * 
     */

    public static function createClass ($className, $classGroup = null): int
    {
        return winforms_getClass ($className, $classGroup);
    }

    /**
     * * Проверка объекта на существование
     * 
     * @param int $selector - указатель на проверяемый объект
     * 
     * @return bool - возвращает true, если объект существует, и false в противном случае
     * 
     * $button = VoidEngine::createObject ('System.Windows.Forms.Button', 'System.Windows.Forms');
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
    * @param mixed $objectName - полное название объекта
    * [@param mixed $objectGroup = null] - полное пространство имён объекта
    * 
    * @return mixed - возвращает указатель на объект типа объекта или false в случае ошибки
    * 
    */

    public static function objectType ($objectName, $objectGroup = null)
    {
        return winforms_typeof ($objectName, $objectGroup);
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
     * $selector = VoidEngine::createObject ('System.Windows.Forms.Button', 'System.Windows.Forms');
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
     * $selector = VoidEngine::createObject ('System.Windows.Forms.Button', 'System.Windows.Forms');
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
     * $selector = VoidEngine::createClass ('System.Windows.Forms.MessageBox', 'System.Windows.Forms');
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
     * @param string $code - PHP код без тэгов
     * 
     * $selector = VoidEngine::createObject ('System.Windows.Forms.Button', 'System.Windows.Forms');
     * VoidEngine::setObjectEvent ($selector, 'Click', 'VoidEngine\pre (123);');
     * 
     */

    public static function setObjectEvent (int $selector, string $eventName, string $code): void
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
     * $selector = VoidEngine::createObject ('System.Windows.Forms.Button', 'System.Windows.Forms');
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
     * $selector = VoidEngine::createObject ('System.Windows.Forms.Button', 'System.Windows.Forms');
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
     * @return array - возвращает список ошибок компиляции
     * 
     */

    public static function compile (string $savePath, string $iconPath, string $phpCode, string $productDescription = null, string $productName = null, string $productVersion = null, string $companyName = null, string $copyright = null, string $callSharpCode = '', string $declareSharpCode = '', WFObject $dictionary = null, WFObject $assemblies = null): array
    {
        if ($dictionary === null)
            $dictionary = new WFObject ('System.Collections.Generic.Dictionary`2[System.String,System.String]', null);

        if ($assemblies === null)
            $assemblies = getNetArray ('System.String');

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

        return (new WFClass ('WinForms_PHP.WFCompiler', null))->compile ($savePath, $iconPath, $phpCode, $productDescription, $productName, $productVersion, $companyName, $copyright, $callSharpCode, $declareSharpCode, $dictionary, $assemblies)->names;
    }
}

class EngineAdditions
{
    public static function loadModule (string $path): bool
    {
        try
        {
            (new WFClass ('System.Reflection.Assembly', 'mscorlib'))->loadFrom ($path);
        }

        catch (\Throwable $e)
        {
            return false;
        }

        return true;
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
        return is_int ($value) && VoidEngine::objectExists ($value) && $value != $selfSelector ?
            new WFObject ($value) : $value;
    }

    public static function uncoupleSelector ($value)
    {
        return $value instanceof WFObject ?
            $value->selector : $value;
    }
}

class WFObject implements \ArrayAccess
{
    protected $selector;
    protected $name;

    public function __construct ($object, ?string $classGroup = 'auto', ...$args)
    {
        if (is_string ($object))
            $this->selector = VoidEngine::createObject ($object, $classGroup == 'auto' ?
                substr ($object, 0, strrpos ($object, '.')) : $classGroup, ...$args);

        elseif (is_int ($object) && VoidEngine::objectExists ($object))
            $this->selector = $object;

        else throw new \Exception ('$object parameter must be string or object selector');
    }
    
    public function __get ($name)
	{
        if (method_exists ($this, $method = "get_$name"))
            $value = $this->$method ();
            
        elseif (substr ($name, -5) == 'Event')
            $value = Events::getObjectEvent ($this->selector, substr ($name, 0, -5));

        elseif (property_exists ($this, $name))
            $value = $this->$name;
        
        else switch (strtolower ($name))
        {
            case 'count':
            case 'length':
                try
                {
                    return $this->getProperty ('Count');
                }

                catch (\WinFormsException $e)
                {
                    return $this->getProperty ('Length');
                }
            break;

            case 'list':
                $size = $this->count;
                $list = [];
                
				for ($i = 0; $i < $size; ++$i)
                    $list[] = EngineAdditions::coupleSelector (VoidEngine::getArrayValue ($this->selector, $i));
                
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

            default:
                $value = $this->getProperty ($name);
            break;
        }

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
                return $value instanceof WFObject ?
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
            return EngineAdditions::uncoupleSelector ($arg);
        }, $args);

        return EngineAdditions::coupleSelector ($this->callMethod ($method, ...$args), $this->selector);
    }

    public function addRange (array $values, $assoc = false): void
    {
        foreach ($values as $id => $value)
            $this->offsetSet ($assoc ? $id : null, $value);
    }
    
    public function offsetSet ($index, $value)
	{
        return $index === null ?
            $this->callMethod ('Add', EngineAdditions::uncoupleSelector ($value)) :
            $this->callMethod ('Insert', $index, EngineAdditions::uncoupleSelector ($value));
    }
	
	public function offsetGet ($index)
	{
		return EngineAdditions::coupleSelector (VoidEngine::getArrayValue ($this->selector, $index), $this->selector);
    }
	
	public function offsetUnset ($index): void
	{
		$this->callMethod ('RemoveAt', $index);
    }
    
    public function offsetExists ($index): bool
    {
        try
        {
            $this->offsetGet ($index);
        }

        catch (\Exception $e)
        {
            return false;
        }

        return true;
    }
	
	public function indexOf ($value): int
	{
		return $this->callMethod ('IndexOf', EngineAdditions::uncoupleSelector ($value));
    }
    
    public function lastIndexOf ($value): int
	{
		return $this->callMethod ('LastIndexOf', EngineAdditions::uncoupleSelector ($value));
	}
	
	public function contains ($value): bool
	{
		return $this->callMethod ('Contains', EngineAdditions::uncoupleSelector ($value));
    }

    public function foreach (\Closure $callback, string $type = null): void
    {
        $size = $this->count;

        for ($i = 0; $i < $size; ++$i)
            $callback (EngineAdditions::coupleSelector (VoidEngine::getArrayValue ($this->selector, $type !== null ? [$i, $type] : $i), $this->selector), $i);
    }

    public function where (\Closure $comparator, string $type = null): array
    {
        $size   = $this->count;
        $return = [];

        for ($i = 0; $i < $size; ++$i)
            if ($comparator ($value = EngineAdditions::coupleSelector (VoidEngine::getArrayValue ($this->selector, $type !== null ? [$i, $type] : $i), $this->selector), $i))
                $return[] = $value;

        return $return;
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

    public function get_name ()
	{
		try
		{
			return $this->getProperty ('Name');
        }
        
		catch (\WinFormsException $e)
		{
			return $this->name;
		}
	}
	
	public function set_name (string $name)
	{
		try
		{
			$this->setProperty ('Name', $name);
        }
        
		catch (\WinFormsException $e)
		{
			$this->name = $name;
		}
	}

    public function __toString (): string
    {
        return $this->callMethod ('ToString');
    }
}

class WFClass extends WFObject
{
    protected $selector;

    public function __construct ($class, ?string $classGroup = 'auto')
    {
        if (is_string ($class))
            $this->selector = VoidEngine::createClass ($class, $classGroup == 'auto' ?
                substr ($class, 0, strrpos ($class, '.')) : $classGroup
            );

        elseif (is_int ($class) && VoidEngine::objectExists ($class))
            $this->selector = $class;

        else throw new \Exception ('$class parameter must be string or class selector');
    }
}
