<?php

/**
 * VoidStudio debugging module
 * @author Podvirnyy Nikita
 * 
 */

namespace VoidEngine;

$debugger = new class
{
    static function debugOutput ($data, $delay = false): void
    {
        file_put_contents ('__debug_answer', json_encode ([
            'timestamp' => time (),
            'data'      => $data
        ], JSON_PRETTY_PRINT));

        while (file_exists ('__debug_answer'))
            usleep (100);
    }

    static function seekObjects (WFObject $object): array
    {
        $objects = [$object->selector];

        try
        {
            $object->controls->foreach (function ($value) use (&$objects)
            {
                $objects = array_merge ($objects, self::seekObjects ($value));
            });

            return array_unique ($objects);
        }

        catch (\Throwable $e)
        {
            return $objects;
        }
    }
};

set_error_handler (function (...$args) use ($debugger)
{
    $debugger::debugOutput ([
        'type' => 'errorCatched',
        'info' => $args
    ], true);
});

set_exception_handler (function (...$args) use ($debugger)
{
    $debugger::debugOutput ([
        'type' => 'exceptionCatched',
        'info' => $args
    ], true);
});

setTimer (500, function () use ($debugger)
{
    $debug = &$GLOBALS['__DEBUG'];

    $components = crc32 (serialize ([Components::$events, Components::$components]));
    Components::cleanJunk ();

    if (crc32 (serialize ([Components::$events, Components::$components])) != $components)
        $debugger::debugOutput ([
            'type' => 'beginJunkCatching'
        ], true);

    elseif (file_exists ('__debug_request'))
    {
        $request = json_decode (file_get_contents ('__debug_request'), true);
        unlink ('__debug_request');

        if ($request['timestamp'] > $debug['last_timestamp'])
        {
            $debug['last_timestamp'] = $request['timestamp'];

            switch ($request['command'])
            {
                case 'get_objects':
                    $referenced = [];

                    foreach (Components::$components as $selector => $component)
                        $referenced = array_merge ($referenced, array_diff ($debugger::seekObjects ($component), $component->getType ()->isSubclassOf (VoidEngine::objectType ('System.Windows.Forms.Form', 'System.Windows.Forms')) ? 
                            [] : [$selector]));

                    // array_slice потому, что первым компонентом в списке всегда будет таймер debug hooker'а
                    $debugger::debugOutput (array_slice (array_map (function ($object) use ($referenced)
                    {
                        return array_merge ($object->__debugInfo (), [
                            'status' => array_search ($object->selector, $referenced) !== false ? 1 : (
                                $object->getType ()->isSubclassOf (VoidEngine::objectType ('System.Windows.Forms.Control', 'System.Windows.Forms')) ? 0 : 2
                            ),

                            'name' => $object->name
                        ]);
                    }, Components::$components), 1));
                break;

                case 'bulb_objects':
                    foreach ($request['arguments']['selectors'] as $selector)
                        try
                        {
                            if (!isset ($debug['colors'][$selector]))
                                $debug['colors'][$selector] = VoidEngine::getProperty ($selector, ['BackColor', 'color']);

                            VoidEngine::setProperty ($selector, 'BackColor', [clYellow, 'color']);
                        }

                        catch (\Throwable $e)
                        {
                            continue;
                        }
                break;

                case 'unbulb_objects':
                    foreach ($request['arguments']['selectors'] as $selector)
                        try
                        {
                            VoidEngine::setProperty ($selector, 'BackColor', [$debug['colors'][$selector], 'color']);

                            unset ($debug['colors'][$selector]);
                        }

                        catch (\Throwable $e)
                        {
                            continue;
                        }
                break;

                case 'remove_objects':
                    foreach ($request['arguments']['selectors'] as $selector)
                        try
                        {
                            VoidEngine::callMethod ($selector, 'Dispose');
                        }
                        
                        catch (\Throwable $e)
                        {
                            continue;
                        }

                    VoidEngine::removeObjects (...$request['arguments']['selectors']);
                break;
            }
        }
    }
});
