<?php

namespace VoidEngine;

function err_status (bool $status = null): bool
{
    $oldStatus = $GLOBALS['__debug']['error_status'];

    if ($status !== null)
        $GLOBALS['__debug']['error_status'] = $status;

    return $oldStatus;
}

function err_no (): bool
{
    return err_status (false);
}

function err_yes (): bool
{
    return err_status (true);
}

function run (string $path, ...$args)
{
    return (new Process)->start ($path, ...$args);
}

function vbs_exec (string $code)
{
    file_put_contents ($path = getenv ('temp') .'/'. crc32 ($code) .'.vbs', $code);

    (new \COM ('WScript.Shell'))->Run ($path, 0, true);

    unlink ($path);
}

function php_errors_check (string $code): ?array
{
    try
    {
        eval ('return; '. $code);
    }

    catch (\Throwable $e)
    {
        return [
            'text' => $e->getMessage (), 
			'line' => $e->getLine ()
        ];
    }

    return null;
}

function enum (string $name): array
{
    return [
        substr ($name, strrpos ($name, '.') + 1),
        ($name = substr ($name, 0, strrpos ($name, '.'))) .', '. substr ($name, 0, strrpos ($name, '.'))
    ];
}

function getNetArray (string $type, array $items = []): WFObject
{
    $array = (new WFClass ('System.Array', null))
        ->createInstance (VoidEngine::objectType ($type), $size = sizeof ($items));

    for ($i = 0; $i < $size; ++$i)
        $array[$i] = array_shift ($items);
    
    return $array;
}

function dir_create (string $path, int $mode = 0777): void
{
    if (!is_dir ($path))
        mkdir ($path, $mode, true);
}

function dir_delete (string $path): bool
{
    if (!is_dir ($path))
        return false;

    foreach (array_slice (scandir ($path), 2) as $file)
        if (is_dir ($file = $path .'/'. $file))
        {
            dir_delete ($file);

            if (is_dir ($file))
                rmdir ($file);
        }

        else unlink ($file);

    rmdir ($path);

    return true;
}

function dir_clean (string $path): void
{
    dir_delete ($path);
    dir_create ($path);
}

function dir_copy (string $from, string $to): bool
{
    if (!is_dir ($from))
        return false;

    if (!is_dir ($to))
        dir_create ($to);

    foreach (array_slice (scandir ($from), 2) as $file)
        if (is_dir ($f = $from .'/'. $file))
            dir_copy ($f, $to .'/'. $file);

        else copy ($f, $to .'/'. $file);

    return true;
}

function getARGBColor (string $color)
{
    return (new WFClass ('System.Drawing.ColorTranslator'))->fromHtml ($color);
}

function replaceSl (string $string): string
{
    return str_replace ('\\', '/', $string);
}

function replaceSr (string $string): string
{
    return str_replace ('/', '\\', $string);
}

function basenameNoExt (string $path): string
{
    return pathinfo ($path, PATHINFO_FILENAME);
}

function file_ext (string $path): string
{
    return strtolower (pathinfo ($path, PATHINFO_EXTENSION));
}

function filepathNoExt (string $path): string
{
    return dirname ($path) .'/'. basenameNoExt ($path);
}

function array_first (array $array)
{
    return array_shift ($array);
}

function array_end (array $array)
{
    return array_pop ($array);
}

function substr_icount (string $haystack, string $needle, ...$params): int
{
	return substr_count (strtolower ($haystack), strtolower ($needle), ...$params);
}

function str_replace_assoc (string $subject, array $replacements): string
{
	return str_replace (array_keys ($replacements), array_values ($replacements), $subject);
}

function pre (...$args): void
{
	message (sizeof ($args) < 2 ? current ($args) : $args);
}

function messageBox (string $message, string $caption = '', ...$args)
{
    return (new MessageBox)->show ($message, $caption, ...$args);
}

class Components
{
    static array $components = [];

    public static function addComponent (int $selector, object $object): void
    {
        self::$components[$selector] = $object;
    }

    public static function getComponent (int $selector)
    {
        return isset (self::$components[$selector]) ?
            self::$components[$selector] : false;
    }

    public static function componentExists (int $selector): bool
    {
        return isset (self::$components[$selector]);
    }

    public static function removeComponent (int $selector): void
    {
        unset (self::$components[$selector]);
    }

    public static function cleanJunk (): void
    {
        // TODO: более строгие правила очистки мусорных объектов
        
        foreach (self::$components as $selector => $object)
        {
            try
            {
                if ($object->getType ()->isSubclassOf (VoidEngine::objectType ('System.Windows.Forms.Form', 'System.Windows.Forms')))
                    continue;
            }

            catch (\Exception $e) {}
            
            VoidEngine::destructObject ($selector);

            if (!VoidEngine::objectExists ($selector))
                unset (self::$components[$selector]);
        }
    }
}

function _c (int $selector)
{
    return Components::getComponent ($selector);
}

function c ($name, bool $returnAllSimilarObjects = false)
{
    if (is_int ($name) && is_object ($object = _c ($name)))
        return $object;

    else
    {
        $path    = explode ('->', $name);
        $similar = [];

        foreach (Components::$components as $object)
            try
            {
                if ($object->name == end ($path))
                {
                    if (sizeof ($path) > 1)
                        try
                        {
                            if (is_object ($parent = _c($object->parent->selector)))
                            {
                                if (c(join ('->', array_slice ($path, 0, -1))) == $parent)
                                {
                                    if ($returnAllSimilarObjects)
                                        $similar[] = $object;

                                    else return $object;
                                }

                                else continue;
                            }

                            else continue;
                        }

                        catch (\Throwable $e)
                        {
                            continue;
                        }

                    else
                    {
                        if ($returnAllSimilarObjects)
                            $similar[] = $object;

                        else return $object;
                    }
                }
            }

            catch (\Exception $e)
            {
                continue;
            }

        if (sizeof ($path) == 2)
        {
            $objects = c($path[1], true);

            if (is_array ($objects))
            {
                foreach ($objects as $id => $object)
                    try
                    {
                        while (is_object ($parent = _c($object->parent->selector)))
                        {
                            if ($parent->getType ()->isSubclassOf (VoidEngine::objectType ('System.Windows.Forms.Form', 'System.Windows.Forms')) && $parent->name == $path[0])
                                return $objects[$id];

                            else $object = $parent;
                        }
                    }

                    catch (\Throwable $e)
					{
						continue;
					}

                return false;
            }

            else return false;
        }

        else return $returnAllSimilarObjects && sizeof ($similar) > 0 ?
            $similar : false;
    }
}

function setTimer (int $interval, callable $function): Timer
{
    $timer = new Timer;
    $timer->interval = $interval;
    
    $timer->tickEvent = function ($self) use ($function)
    {
        call_user_func ($function, $self);
    };
    
	$timer->start ();
    
    return $timer;
}

// FIXME: выполняется несколько раз, а не единожды
function setTimeout (int $timeout, callable $function): Timer
{
    $timer = new Timer;
    $timer->interval = $timeout;
    
    $timer->tickEvent = function ($self) use ($function)
    {
        call_user_func ($function, $self);

        $self->stop ();
    };
    
    $timer->start ();
    
	return $timer;
}

class Clipboard
{
    public static function getText ()
    {
        return (new WFClass ('System.Windows.Forms.Clipboard'))->getText ();
    }
    
    public static function setText (string $text): void
    {
        (new WFClass ('System.Windows.Forms.Clipboard'))->setText ($text);
    }
    
    public static function getFiles (): array
    {
        return (new WFClass ('System.Windows.Forms.Clipboard'))->getFileDropList ()->list;
    }
    
    public static function setFiles (array $files): void
    {
        $collection = new WFObject ('System.Collections.Specialized.StringCollection');

        foreach ($files as $file)
            $collection->add ((string) $file);

        (new WFClass ('System.Windows.Forms.Clipboard'))->setFileDropList ($collection);
        VoidEngine::removeObjects ($collection->selector);
    }
}

class Cursor
{
    protected $cursor;

    public function __construct (int $handle = null)
    {
        $handle !== null ?
            $this->cursor = new WFObject ('System.Windows.Forms.Cursor', 'auto', $handle) :
            $this->cursor = new WFClass ('System.Windows.Forms.Cursor');
    }

    public function getPosition (): array
    {
        $pos = $this->cursor->position;

        return [
            $pos->x,
            $pos->y
        ];
    }
}

function get_cursor_x (int $handle = null): int
{
    return (new Cursor ($handle))->getPosition ()[0];
}

function get_cursor_y (int $handle = null): int
{
    return (new Cursor ($handle))->getPosition ()[1];
}

function get_cursor_pos (int $handle = null): array
{
    return (new Cursor ($handle))->getPosition ();
}

set_error_handler (function ($no, $str, $file, $line)
{
    // Мог ли я здесь сделать более адекватный код с использованием pow/sqrt? Да, мог
    // Почему не сделал? Скорость важнее
    static $errarr = [
        1     => 'E_ERROR',
        2     => 'E_WARNING',
        4     => 'E_PARSE',
        8     => 'E_NOTICE',
        16    => 'E_CORE_ERROR',
        32    => 'E_CORE_WARNING',
        64    => 'E_COMPILE_ERROR',
        128   => 'E_COMPILE_WARNING',
        256   => 'E_USER_ERROR',
        512   => 'E_USER_WARNING',
        1024  => 'E_USER_NOTICE',
        2048  => 'E_STRICT',
        4096  => 'E_RECOVERABLE_ERROR',
        8192  => 'E_DEPRECATED',
        16384 => 'E_USER_DEPRECATED'
    ];

    if ($GLOBALS['__debug']['error_status'])
        message ([
            'type'      => $errarr[$no],
            'text'      => $str,
            'file'      => $file,
            'line'      => $line,
            'backtrace' => debug_backtrace ()
        ], 'PHP Script Error');
});
