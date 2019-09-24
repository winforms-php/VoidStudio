<?php

namespace VoidEngine;

class Events
{
    public static function setObjectEvent (int $object, string $eventName, callable $function)
    {
        \VoidCore::setEvent ($object, $eventName, function ($sender, ...$args) use ($function)
		{
            try
			{
                foreach ($args as $id => $arg)
                    $args[$id] = EngineAdditions::coupleSelector ($arg);
                
                return $function (($e = _c($sender)) !== false ?
                    $e : new WFObject ($sender), ...$args);
            }
            
			catch (\Throwable $e)
			{
                message ([
                    'type'  => get_class ($e),
                    'text'  => $e->getMessage (),
                    'file'  => $e->getFile (),
                    'line'  => $e->getLine (),
                    'code'  => $e->getCode (),
                    'trace' => $e->getTraceAsString ()
                ], 'PHP Critical Error');
            }
        });
    }

    public static function removeObjectEvent (int $object, string $eventName)
    {
        \VoidCore::removeEvent ($object, $eventName);
    }
}
