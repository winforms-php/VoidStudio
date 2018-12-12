<?php

/*
    Класс, отвечающий за работу с событиями
*/

namespace VoidEngine;

class Events
{
    static $events = [];

    static function setObjectEvent (Component $object, string $eventName, $function)
    {
        if (!($object instanceof Component))
            return false;

        $selector = $object->selector;

        self::$events[$selector][$eventName] = $function;

        VoidEngine::setObjectEvent ($selector, $eventName, "if (isset (VoidEngine\Events::\$events['$selector']['$eventName'])) VoidEngine\Events::\$events['$selector']['$eventName'] (VoidEngine\Components::getComponent ('$selector'), isset (\$args) ? \$args : false);");
    }

    static function removeObjectEvent (Component $object, string $eventName)
    {
        $selector = $object->selector;

        VoidEngine::removeObjectEvent ($selector, $eventName);
        unset (self::$events[$selector][$eventName]);
    }

    static function getObjectEvent (Component $object, string $eventName)
    {
        $selector = $object->selector;

        return self::$events[$selector][$eventName];
    }
}

abstract class EventArgs
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
		
		else throw new \Exception ("The \"$name\" property is missing");
	}
	
    final public function __set ($name, $value)
	{
		if (method_exists ($this, $method = "set_$name"))
			return $this->$method ($value);
		
        else throw new \Exception ("The \"$name\" property is missing");
	}
	
	final protected function getProperty (string $name, string $type)
    {
        return VoidEngine::getProperty ($this->selector, $name, $type);
    }
	
    final protected function setProperty (string $name, $value, string $type)
    {
        VoidEngine::setProperty ($this->selector, $name, $value, $type);
    }
	
}

?>
