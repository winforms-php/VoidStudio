<?php

namespace VoidEngine;

function nothing (): void {}

function run (string $path): WFClass
{
    $process = new ObjectType ('System.Diagnostics.Process', 'System');
    $process->token = 'b77a5c561934e089';

    $process = new WFClass ($process);
    $process->start ($path);

    return $process;
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

        return null;
    }

    catch (\ParseError $e)
    {
        return [
            'text' => $e->getMessage (), 
			'line' => $e->getLine ()
        ];
    }
}

function text (string $text, string $encoding = 'Windows-1251'): string
{
    return mb_convert_encoding ($text, $encoding);
}

function enum (string $name): array
{
    return [
        substr ($name, strrpos ($name, '.') + 1),
        ($name = substr ($name, 0, strrpos ($name, '.'))) .', '. substr ($name, 0, strrpos ($name, '.'))
    ];
}

function dir_create (string $path): void
{
    if (!is_dir ($path))
    {
        $path = explode ('/', replaceSl ($path));
        $dir  = '';

        foreach ($path as $subdir)
            if (!is_dir ($dir = ($dir ? "$dir/" : '') . $subdir))
                mkdir ($dir);
    }
}

function dir_delete (string $path): bool
{
    if (!is_dir ($path))
        return false;

    foreach (array_slice (scandir ($path), 2) as $file)
        if (is_dir ($file = "$path/$file"))
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
        if (is_dir ("$from/$file"))
        {
            if (!is_dir ("$to/$file"))
                mkdir ("$to/$file");

            dir_copy ("$from/$file", "$to/$file");
        }

        else copy ("$from/$file", "$to/$file");

    return true;
}

function getARGBColor (string $color)
{
    $converter = new ObjectType ('System.Drawing.ColorTranslator');
    $converter->token = 'b03f5f7f11d50a3a';

    return (new WFClass ($converter))->fromHtml ($color);
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
	if (sizeof ($args) < 2)
		$args = current ($args);
	
	message (print_r ($args, true));
}

function messageBox (string $message, string $caption = '', ...$args)
{
    return (new MessageBox)->show ($message, $caption, ...$args);
}

class Components
{
    static $components = [];
    static $events = [];

    static function addComponent (int $selector, object $object): void
    {
        self::$components[$selector] = $object;
        self::$events[$selector] = [];
    }

    static function getComponent (int $selector)
    {
        return isset (self::$components[$selector]) ?
            self::$components[$selector] : false;
    }

    static function componentExists (int $selector): bool
    {
        return isset (self::$components[$selector]);
    }

    static function setComponentEvent (int $selector, string $eventName, string $code): void
    {
        self::$events[$selector][$eventName] = $code;
    }

    static function getComponentEvent (int $selector, string $eventName)
    {
        return isset (self::$events[$selector][$eventName]) ?
            self::$events[$selector][$eventName] : false;
    }

    static function removeComponentEvent (int $selector, string $eventName): void
    {
        unset (self::$events[$selector][$eventName]);
    }

    static function removeComponent (int $selector): void
    {
        unset (self::$components[$selector], self::$events[$selector]);
    }

    static function cleanJunk (): void
    {
        foreach (self::$components as $selector => $object)
        {
            // TODO: более строгие правила очистки мусорных объектов
            VoidEngine::destructObject ($selector);

            if (!VoidEngine::objectExists ($selector))
            {
                unset (self::$components[$selector]);

                if (isset (self::$events[$selector]))
                    unset (self::$events[$selector]);
            }
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
                            if ($parent->getType ()->toString () == 'System.Windows.Forms.Form' && $parent->name == $path[0])
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
    public static function getText (): string
    {
        return (new WFClass ('System.Windows.Forms.Clipboard'))->getText ();
    }
    
    public static function setText (string $text): void
    {
        (new WFClass ('System.Windows.Forms.Clipboard'))->setText ($text);
    }
    
    public static function getFiles (): array
    {
        $array = (new WFClass ('System.Windows.Forms.Clipboard'))->getFileDropList ();
        $size  = VoidEngine::getProperty ($array, 'Count');
        $files = [];

        for ($i = 0; $i < $size; ++$i)
            $files[] = VoidEngine::getArrayValue ($array, $i);

        VoidEngine::removeObjects ($array);

        return $files;
    }
    
    public static function setFiles (array $files): void
    {
        $collection = VoidEngine::createObject (new ObjectType ('System.Collections.Specialized.StringCollection'));

        foreach ($files as $file)
            VoidEngine::callMethod ($collection, 'Add', (string) $file);

        (new WFClass ('System.Windows.Forms.Clipboard'))->setFileDropList ($collection);
        VoidEngine::removeObjects ($collection);
    }
}

class Cursor
{
    protected $cursor;

    public function __construct (int $handle = null)
    {
        $handle !== null ?
            $this->cursor = new WFObject ('System.Windows.Forms.Cursor', null, false, $handle) :
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
    $cursor = new Cursor ($handle);

    return $cursor->getPosition ()[0];
}

function get_cursor_y (int $handle = null): int
{
    $cursor = new Cursor ($handle);

    return $cursor->getPosition ()[1];
}

function get_cursor_pos (int $handle = null): array
{
    $cursor = new Cursor ($handle);

    return $cursor->getPosition ();
}

set_error_handler (function (...$args)
{
    pre ($args);
});

set_exception_handler (function (...$args)
{
    pre ($args);
});

?>
