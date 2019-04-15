<?php

namespace VoidEngine;

class Events
{
    static $events = [];

    static function setObjectEvent (int $object, string $eventName, $function)
    {
        self::$events[$object][$eventName] = $function;

        VoidEngine::setObjectEvent ($object, $eventName, "if (VoidEngine\Events::getObjectEvent ('$object', '$eventName') !== false) VoidEngine\Events::getObjectEvent ('$object', '$eventName') (VoidEngine\_c('$object'), isset (\$args) ? (is_int (\$args) && VoidEngine\VoidEngine::objectExists (\$args) ? new VoidEngine\EventArgs (\$args) : \$args) : false);");
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
        return self::$events[$object][$eventName] ?: false;
    }

    static function getObjectEvents (int $object)
    {
        return self::$events[$object] ?: false;
    }
}

class EventArgs extends WFObject
{
	public function __construct (int $selector)
    {
        $this->selector = $selector;
    }
}
