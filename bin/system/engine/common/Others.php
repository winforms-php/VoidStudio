<?php

namespace VoidEngine;

function nothing (): void {}

function run (string $path)
{
    $process = new ObjectType ('System.Diagnostics.Process', 'System');
    $process->token = 'b77a5c561934e089';

    $process = new WFClass ($process);
    $process->start ($path);

    return $process;
}

function text (string $text): string
{
    return mb_convert_encoding ($text, 'Windows-1251');
}

function dir_create (string $path): void
{
    if (!is_dir ($path))
    {
        $path = explode ('/', replaceSl ($path));
        $dir  = '';

        foreach ($path as $id => $subdir)
            if (!is_dir ($dir = ($dir ? "$dir/" : '') . $subdir))
                mkdir ($dir);
    }
}

function dir_delete (string $path): bool
{
    if (!is_dir ($path))
        return false;

    $files = array_slice (scandir ($path), 2);

    foreach ($files as $id => $file)
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
    if (!is_dir ($from) || !is_dir ($to))
        return false;

    $files = array_slice (scandir ($from), 2);

    foreach ($files as $id => $file)
        if (is_dir ("$from/$file"))
        {
            if (!is_dir ("$to/$file"))
                mkdir ("$to/$file");

            dir_copy ("$from/$file", "$to/$file");
        }

        else copy ("$from/$file", "$to/$file");

    return true;
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

    static function cleanJunk (): array
    {
        $junk = [];

        foreach (self::$components as $selector => $object)
            if (!VoidEngine::objectExists ($selector))
            {
                $junk[$selector] = $object;

                unset (self::$components[$selector]);

                if (isset (self::$events[$selector]))
                    unset (self::$events[$selector]);
            }

        return $junk;
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

        foreach (Components::$components as $selector => $object)
            if (($object instanceof Control || method_exists ($object, 'get_name') || property_exists ($object, 'name')) && $object->name == end ($path))
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
                            // TODO $parent может быть и WFObject с типом System.Windows.Forms.Form

                            if ($parent instanceof Form && $parent->name == $path[0])
                                return $objects[$id];

                            else $object = $parent;
                        }
                    }

                    catch (\Throwable $e) {}

                return false;
            }

            else return false;
        }

        else
        {
            if ($returnAllSimilarObjects && sizeof ($similar) > 0)
                return $similar;
            
            else return false;
        }
    }
}

function setTimer (int $interval, $function): Timer
{
    $timer = new Timer;
    $timer->interval = $interval;
    
    $timer->tickEvent = function ($self) use ($function)
    {
        is_callable ($function) ?
            call_user_func ($function, $self) :
            eval ($function);
    };
    
	$timer->start ();
    
    return $timer;
}

function setTimeout (int $interval, $function): Timer
{
    $timer = new Timer;
    $timer->interval = $interval;
    
    $timer->tickEvent = function ($self) use ($function)
    {
        is_callable ($function) ?
            call_user_func ($function, $self) :
            eval ($function);

        $self->dispose ();
    };
    
    $timer->start ();
    
	return $timer;
}

class Clipboard
{
    static $clipboard;

    public static function getText (): string
    {
        if (!isset (self::$clipboard))
            self::$clipboard = new WFClass ('System.Windows.Forms.Clipboard');

        return self::$clipboard->getText ();
    }
    
    public static function setText (string $text): void
    {
        if (!isset (self::$clipboard))
            self::$clipboard = new WFClass ('System.Windows.Forms.Clipboard');

        self::$clipboard->setText ($text);
    }
    
    public static function getFiles (): array
    {
        if (!isset (self::$clipboard))
            self::$clipboard = new WFClass ('System.Windows.Forms.Clipboard');

        $array = self::$clipboard->getFileDropList ();
        $size  = VoidEngine::getProperty ($array, 'Count');
        $files = [];

        for ($i = 0; $i < $size; ++$i)
            $files[] = VoidEngine::getArrayValue ($array, $i);

        VoidEngine::removeObject ($array);

        return $files;
    }
    
    public static function setFiles (array $files): void
    {
        if (!isset (self::$clipboard))
            self::$clipboard = new WFClass ('System.Windows.Forms.Clipboard');

        $collection = VoidEngine::createObject (new ObjectType ('System.Collections.Specialized.StringCollection'));

        foreach ($files as $file)
            VoidEngine::callMethod ($collection, 'Add', (string) $file);

        self::$clipboard->setFileDropList ($collection);
        VoidEngine::removeObject ($collection);
    }
}

class Items extends \ArrayObject
{
    protected $selector;
	
	public function __construct (int $selector)
	{
		$this->selector = $selector;
    }
    
    public function __get ($name)
	{
		switch (strtolower ($name))
		{
			case 'count':
                return VoidEngine::getProperty ($this->selector, 'Count');
            break;
				
            case 'list':
                $size = VoidEngine::getProperty ($this->selector, 'Count');
                $list = [];
                
				for ($i = 0; $i < $size; ++$i)
                    $list[] = VoidEngine::getArrayValue ($this->selector, $i);
                     
                return $list;
            break;

            case 'names':
                $size = VoidEngine::getProperty ($this->selector, 'Count');
                $names = [];
                
                for ($i = 0; $i < $size; ++$i)
                    try
                    {
                        $names[] = VoidEngine::getProperty (VoidEngine::getArrayValue ($this->selector, [$i, 'object']), 'Text');
                    }

                    catch (\WinFormsException $e)
                    {
                        $names[] = VoidEngine::getArrayValue ($this->selector, [$i, 'string']);
                    }
                
                return $names;
            break;

            case 'selector':
                return $this->selector;
            break;
		}
    }
	
	public function add ($value)
	{
		$this->offsetSet (null, $value);
	}
	
	public function append ($value)
	{
		$this->offsetSet (null, $value);
	}
	
	public function offsetSet ($index, $value)
	{
        return $index === null ?
            VoidEngine::callMethod ($this->selector, 'Add', $value instanceof WFObject ? $value->selector : $value) :
            VoidEngine::callMethod ($this->selector, 'Insert', $index, $value instanceof WFObject ? $value->selector : $value);
	}
	
	public function offsetGet ($index)
	{
		return VoidEngine::getArrayValue ($this->selector, $index);
	}
	
	public function addRange (array $items): void
	{
		array_map ([$this, 'append'], $items);
	}
	
	public function offsetUnset ($index): void
	{
		VoidEngine::callMethod ($this->selector, 'RemoveAt', $index);
	}
	
	public function remove ($index): void
	{
		$this->offsetUnset ($index);
	}
	
	public function clear (): void
	{
		VoidEngine::callMethod ($this->selector, 'Clear');
	}
	
	public function indexOf ($value): int
	{
		return VoidEngine::callMethod ($this->selector, 'IndexOf', $value instanceof WFObject ? $value->selector : $value);
	}
	
	public function insert ($index, $value)
	{
		$this->offsetSet ($index, $value);
	}
	
	public function contains ($value): bool
	{
		return VoidEngine::callMethod ($this->selector, 'Contains', $value instanceof WFObject ? $value->selector : $value);
    }
}

class Icon extends WFObject
{
    public function __construct (string $file)
    {
        $icon = new ObjectType ('System.Drawing.Icon');
        $icon->token = 'b03f5f7f11d50a3a';

		parent::__construct ($icon);
    }

    public function applyToObject (int $selector): void
	{
		VoidEngine::setProperty ($selector, 'Icon', $this->selector);
	}
	
	public function saveToFile (string $file): void
	{
		VoidEngine::callMethod ($this->selector, 'Save', $file);
	}
}

class Cursor
{
    protected $cursor;

    public function __construct (int $handle = null)
    {
        $handle !== null ?
            $this->cursor = new WFObject (new ObjectType ('System.Windows.Forms.Cursor'), $handle) :
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
