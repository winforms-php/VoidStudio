<?php

namespace VoidEngine;

class EngineAdditions
{
    /**
     * * Компиляция PHP кода
     * 
     * TODO: дополнить описание
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
            $assemblies = getNetArray ('System.String', [
                // CORE_DIR .'/CefSharp.dll',
                CORE_DIR .'/FastColoredTextBox.dll',
                CORE_DIR .'/ScintillaNET.dll'
            ]);

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

    public static function loadModule (string $path): bool
    {
        try
        {
            (new WFClass ('System.Reflection.Assembly', 'mscorlib'))->loadFrom ($path);
        }

        catch (\WinFormsException $e)
        {
            return false;
        }

        return true;
    }

    public static function getProperty (int $selector, string $name): array
    {
        $property = \VoidCore::callMethod (\VoidCore::callMethod ($selector, 'GetType'), 'GetProperty', $name);

        if (!is_int ($property))
            return false;

        try
        {
            $propertyType = \VoidCore::getProperty ($property, ['PropertyType', 'string']);

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
                        \VoidCore::getProperty ($selector, [$name, 'int']);

                        $property = 'int';
                    }

                    catch (\WinFormsException $e)
                    {
                        return [
                            'type'  => 'vrsf',
                            'value' => \VoidCore::exportObject (\VoidCore::getProperty ($selector, [$name, 'object']))
                        ];
                    }
                break;
            }
        }

        catch (\Throwable $e)
        {
            $property = 'object';
        }

        return [
            'type'  => $property,
            'value' => \VoidCore::getProperty ($selector, [$name, $property])
        ];
    }

    public static function getObjectEvents (int $object): array
    {
        $events = [];

        $props = \VoidCore::callMethod (\VoidCore::callMethod ($object, 'GetType'), 'GetEvents');
        $len   = \VoidCore::getProperty ($props, 'Length');

        for ($i = 0; $i < $len; ++$i)
            $events[] = \VoidCore::getProperty (\VoidCore::getArrayValue ($props, $i), 'Name');

        return $events;
    }

    /**
     * При вызове coupleSelector от object->selector указатель может быть обработан в WFObject
     * Тогда получается бесконечный цикл вида object->selector->selector->selector->...
     * Чтобы этого избежать нужно добавить исключение - переменную $selfSelector
     */
    public static function coupleSelector ($value, int $selfSelector = null)
    {
        return is_int ($value) && \VoidCore::objectExists ($value) && $value != $selfSelector ?
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
    protected int $selector = 0;
    protected $name;

    public function __construct ($object, $classGroup = false, ...$args)
    {
        foreach ($args as $id => $arg)
            $args[$id] = EngineAdditions::uncoupleSelector ($arg);

        if (is_string ($object))
        {
            $this->selector = \VoidCore::createObject ($object, $classGroup, ...$args);
            
            /*$this->selector = \VoidCore::createObject ($object, $classGroup == 'auto' ?
                substr ($object, 0, strrpos ($object, '.')) : $classGroup, ...$args);*/
        }

        elseif (is_int ($object) && \VoidCore::objectExists ($object))
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
                    $list[] = EngineAdditions::coupleSelector (\VoidCore::getArrayValue ($this->selector, $i));
                
                return $list;
            break;

            case 'names':
                $size = $this->count;
                $names = [];
                
                for ($i = 0; $i < $size; ++$i)
                    try
                    {
                        $names[] = \VoidCore::getProperty (\VoidCore::getArrayValue ($this->selector, [$i, 'object']), 'Text');
                    }

                    catch (\WinFormsException $e)
                    {
                        $names[] = \VoidCore::getArrayValue ($this->selector, [$i, 'string']);
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
        
        else
        {
            if (is_array ($value) && is_string (current ($value)))
                $value = getNetArray ('System.String', $value);

            $this->setProperty ($name, EngineAdditions::uncoupleSelector ($value));
        }
    }
	
	public function __call ($method, $args)
	{
        $args = array_map (function ($arg)
        {
            return EngineAdditions::uncoupleSelector ($arg);
        }, $args);

        return EngineAdditions::coupleSelector ($this->callMethod ($method, ...$args), $this->selector);
    }

    public function addRange ($values, $assoc = false): void
    {
        if (is_array ($values))
            foreach ($values as $id => $value)
                $this->offsetSet ($assoc ? $id : null, $value);

        else $this->callMethod ('AddRange', EngineAdditions::uncoupleSelector ($values));
    }
    
    public function offsetSet ($index, $value)
	{
        try
        {
            return $index === null ?
                $this->callMethod ('Add', EngineAdditions::uncoupleSelector ($value)) :
                $this->callMethod ('Insert', $index, EngineAdditions::uncoupleSelector ($value));
        }

        catch (\Throwable $e)
        {
            return $index === null ?
                \VoidCore::setArrayValue ($this->selector, $this->count, EngineAdditions::uncoupleSelector ($value)) :
                \VoidCore::setArrayValue ($this->selector, $index, EngineAdditions::uncoupleSelector ($value));
        }
    }
	
	public function offsetGet ($index)
	{
		return EngineAdditions::coupleSelector (\VoidCore::getArrayValue ($this->selector, $index), $this->selector);
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

    public function foreach (callable $callback, string $type = null): void
    {
        $size = $this->count;

        for ($i = 0; $i < $size; ++$i)
            $callback (EngineAdditions::coupleSelector (\VoidCore::getArrayValue ($this->selector, $type !== null ? [$i, $type] : $i), $this->selector), $i);
    }

    public function where (callable $comparator, string $type = null): array
    {
        $size   = $this->count;
        $return = [];

        for ($i = 0; $i < $size; ++$i)
            if ($comparator ($value = EngineAdditions::coupleSelector (\VoidCore::getArrayValue ($this->selector, $type !== null ? [$i, $type] : $i), $this->selector), $i))
                $return[] = $value;

        return $return;
    }
	
    protected function getProperty ($name)
    {
        try
        {
            return \VoidCore::getProperty ($this->selector, $name);
        }

        catch (\WinFormsException $e)
        {
            return \VoidCore::getField ($this->selector, $name);
        }
    }

    protected function setProperty ($name, $value)
    {
        try
        {
            \VoidCore::setProperty ($this->selector, $name, $value);
        }

        catch (\WinFormsException $e)
        {
            \VoidCore::setField ($this->selector, $name, $value);
        }
    }
	
    protected function callMethod ($method, ...$args)
    {
        return \VoidCore::callMethod ($this->selector, $method, ...$args);
    }
	
	protected function getArrayProperty ($name, string $type = null)
	{
        $array  = $this->getProperty ($name);
        $size   = \VoidCore::getProperty ($array, 'Length');
        $return = [];

		for ($i = 0; $i < $size; ++$i)
            $return[] = \VoidCore::getArrayValue ($array, $type === null ? $i : [$i, $type]);
        
        \VoidCore::removeObjects ($array);
        
		return $return;
    }

    public function get_name ()
	{
		try
		{
			return $this->getProperty ('Name');
        }
        
		catch (\Throwable $e)
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
        
		catch (\Throwable $e)
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
    public function __construct ($class, $classGroup = false)
    {
        if (is_string ($class))
            $this->selector = \VoidCore::getClass ($class, $classGroup);

        elseif (is_int ($class) && \VoidCore::objectExists ($class))
            $this->selector = $class;

        else throw new \Exception ('$class parameter must be string or class selector');
    }
}
