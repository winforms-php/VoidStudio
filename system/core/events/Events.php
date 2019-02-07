<?php

/*
    Класс, отвечающий за работу с событиями
*/

namespace VoidEngine;

class Events
{
    static $events = [];

    static function setObjectEvent (int $object, string $eventName, $function)
    {
        self::$events[$object][$eventName] = $function;

        VoidEngine::setObjectEvent ($object, $eventName, "if (VoidEngine\Events::getObjectEvent ('$object', '$eventName') !== false) VoidEngine\Events::getObjectEvent ('$object', '$eventName') (VoidEngine\Components::getComponent ('$object'), isset (\$args) ? \$args : false);");
    }

    static function reserveObjectEvent (int $object, string $eventName)
    {
        self::$events[$object][$eventName] = function ($self) {};

        VoidEngine::setObjectEvent ($object, $eventName, '');
    }

    static function removeObjectEvent (int $object, string $eventName)
    {
        VoidEngine::removeObjectEvent ($object, $eventName);

        unset (self::$events[$object][$eventName]);
    }

    static function getObjectEvent (int $object, string $eventName)
    {
        return isset (self::$events[$object][$eventName]) ?
            self::$events[$object][$eventName] : false;
    }

    static function getObjectEvents (int $object)
    {
        return isset (self::$events[$object]) ?
            self::$events[$object] : false;
    }
}

class EventArgs
{
	protected $selector;
	
    final public function __construct (int $selector)
    {
        $this->selector = $selector;
    }
	
	final public function __get ($name)
	{
		if (method_exists ($this, $method = "get_$name"))
            return $this->$method ();
        
        else return $this->getProperty ($name);
	}
	
    final public function __set ($name, $value)
	{
		if (method_exists ($this, $method = "set_$name"))
            return $this->$method ($value);
        
        else $this->setProperty ($name, $value);
    }
    
    final public function get_selector ()
    {
        return $this->selector;
    }
	
	final protected function getProperty ($name)
    {
        return VoidEngine::getProperty ($this->selector, $name);
    }
	
    final protected function setProperty (string $name, $value)
    {
        VoidEngine::setProperty ($this->selector, $name, $value);
    }
	
}

?>
