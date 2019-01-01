<?php

namespace VoidEngine;

class VoidStudioAPI
{
    static $objects = [];

    static function addObjects (string $group, array $objects)
    {
        self::$objects[$group] = array_merge
        (
            isset (self::$objects[$group]) ?
                self::$objects[$group] : [],

            $objects
        );
    }

    static function getObjects (string $group)
    {
        return isset (self::$objects[$group]) ?
            self::$objects[$group] : false;
    }
}

class VoidStudioBuilder
{
    static function constructVLF () // Нихрена не работает!
    {
        $return = [];

        foreach (VoidStudioAPI::$objects as $group => $objs)
            foreach ($objs as $name => $object)
            {
                $class = get_class ($object);
                $class = substr ($class, ($pos = strrpos ($class, '\\')) !== false ? $pos + 1 : 0);

                $return[$group] .= "$class $name ():\n";

                foreach ((array) $object as $propertyName => $propertyValue)
                    $return[$group] .= "\t$propertyName: ". (
                        is_callable ($propertyValue) ?
                            'function (...$args) {call_user_func_array ('. $func .', ...$args);}' :
                            $propertyValue
                    ) ."\n";

                $return[$group] .= "\n";
            }

        return $return;
    }
}

?>
