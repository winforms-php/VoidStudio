<?php

/*
    Класс-интерфейс VoidEngine'а
    Отвечает за работу с WinForms-ядром со стороны PHP (.NET Framework)
*/

namespace VoidEngine;

class VoidEngine
{
    static function createObject (WFObject $object, ...$args): int
    {
        return winforms_objectcreate ($object->getResourceLine (), ...$args);
    }

    static function removeObject (int ...$selectors): void
    {
        winforms_objectdelete (...$selectors);
    }

    static function buildObject (WFObject $object): int
    {
        return winforms_objectget ($object->getResourceLine ());
    }

    static function objectExists (int $selector): bool
    {
        return winforms_objectexists ($selector);
    }

    static function objectType ($object)
    {
        if ($object instanceof WFObject)
            $object = $object->getResourceLine ();

        elseif (!is_string ($object))
            return false;

        else return winforms_typeof ($object);
    }

    static function loadModule (string $path): void
    {
        $assembly = new WFClass ('System.Reflection.Assembly', 'mscorlib');
        $assembly->LoadFrom ($path);
    }

    static function getProperty (int $selector, string $propertyName, string $type = 'string')
    {
        if ($type == 'auto')
            throw new \Exception ('getProperty function can\'t read "auto" type');

        return winforms_getprop ($selector, $propertyName, $type);
    }

    static function setProperty (int $selector, string $propertyName, $value, string $type = 'auto'): void
    {
        if ($type == 'auto')
            $type = getLogicalVarType ($value);

        winforms_setprop ($selector, $propertyName, $value, $type);
    }

    static function callMethod (int $selector, string $methodName, string $type = 'string', ...$args)
    {
        if ($type == 'auto')
            throw new \Exception ('callMethod function can\'t read "auto" type');

        return winforms_callmethod ($selector, $methodName, $type, ...$args);
    }

    static function getArrayValue (int $selector, int $index, string $type = 'string')
    {
        if ($type == 'auto')
            throw new \Exception ('getArrayValue function can\'t read "auto" type');

        return winforms_getindex ($selector, $index, $type);
    }

    static function setArrayValue (int $selector, int $index, $value, string $type = 'auto'): void
    {
        if ($type == 'auto')
            $type = getLogicalVarType ($value);

        winforms_setindex ($selector, $index, $value, $type);
    }

    static function setObjectEvent (int $selector, string $eventName, string $code = ''): void
    {
        if (self::eventExists ($selector, $eventName))
            self::removeEvent ($selector, $eventName);

        try
        {
            winforms_setevent ($selector, $eventName, $code);

            Components::setComponentEvent ($selector, $eventName, $code);
        }

        catch (\Exception $e)
        {
            throw $e;
        }
    }

    static function eventExists (int $selector, string $eventName): bool
    {
        return winforms_existsevent ($selector, $eventName);
    }

    static function removeEvent (int $selector, string $eventName): void
    {
        winforms_delevent ($selector, $eventName);

        Components::removeComponentEvent ($selector, $eventName);
    }

    static function compile (string $savePath, string $iconPath, string $phpCode): void
    {
        winforms_compile ($savePath, $iconPath, $phpCode);
    }
}

class EngineAdditions
{
    static function getObjectProperties (int $selector, bool $extended = false): array
    {
        $properties = [];

        $type  = VoidEngine::callMethod ($selector, 'GetType', 'object');
        $props = VoidEngine::callMethod ($type, 'GetProperties', 'object');
        $len   = VoidEngine::getProperty ($props, 'Length', 'int');

        for ($i = 0; $i < $len; ++$i)
        {
            $index = VoidEngine::getArrayValue ($props, $i, 'object');
            $name  = VoidEngine::getProperty ($index, 'Name', 'string');

            $property = self::getProperty ($selector, $name);

            $properties[$name] = $extended ?
                $property : $property['value'];
        }

        return $properties;
    }

    static function getProperty (int $selector, string $name): array
    {
        $type         = VoidEngine::callMethod ($selector, 'GetType', 'object');
        $property     = VoidEngine::callMethod ($type, 'GetProperty', 'object', $name, 'string');
        $propertyType = VoidEngine::getProperty ($property, 'PropertyType', 'string');

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

            case 'System.Boolean':
                $property = 'bool';
            break;

            case 'System.Drawing.Color':
                $property = 'color';
            break;

            default:
                try
                {
                    $property = 'int';
                }

                catch (\WinFormsException $e)
                {
                    $property = 'object';
                }
            break;
        }

        return [
            'type'  => $property,
            'value' => VoidEngine::getProperty ($selector, $name, $property)
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

    public function __get ($name)
    {
        return isset ($this->$name) ?
            $this->$name : false;
    }

    public function __set ($name, $value): void
    {
        if (isset ($this->$name))
            $this->$name = $value;
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
            return VoidEngine::getProperty ($this->class, $name, '');

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
        {
            $autoDetectVarType = true;
            $setArgs           = [];
            
            if (substr ($method, strlen ($method) - 2) == 'Ex')
            {
                $autoDetectVarType = false;

                $method = substr ($method, 0, -2);
            }

            foreach ($args as $id => $arg)
            {
                $setArgs[] = $arg;

                if ($autoDetectVarType)
                    $setArgs[] = getLogicalVarType ($arg);
            }

            return VoidEngine::callMethod ($this->class, $method, '', ...$setArgs);
        }

        else throw new \Exception ('Class isn\'t initialized');
	}
}

?>
