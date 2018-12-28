<?php

/*
    Класс, отвечающий за объявление вспомогательных функций
*/

namespace VoidEngine;

function text (string $text)
{
    return iconv ('UTF-8', 'CP1251', $text);
}

function dir_delete (string $path)
{
    if (!is_dir ($path))
        return false;

    $files = array_slice (scandir ($path), 2);

    if (is_array ($files))
        foreach ($files as $id => $file)
            if (is_dir ($file))
                dir_delete ($file);

            else unlink ($file);
}

function dir_copy (string $from, string $to)
{
    if (!is_dir ($from) || !is_dir ($to))
        return false;

    $files = array_slice (scandir ($from), 2);

    if (is_array ($files))
        foreach ($files as $id => $file)
            if (is_dir ("$from/$file"))
            {
                if (!is_dir ("$to/$file"))
                    mkdir ("$to/$file");

                dir_copy ("$from/$file", "$to/$file");
            }

            else copy ("$from/$file", "$to/$file");
}

function run (string $file, int $windowStyle, bool $wait = false)
{
    static $COM;
	
	if (!isset ($COM))
		$COM = new COM ('WScript.Shell');
	
	return $COM->run ($file, $windowStyle, (int) $wait);
}

function replaceSl (string $string)
{
    return str_replace ('\\', '/', $string);
}

function replaceSr (string $string)
{
    return str_replace ('/', '\\', $string);
}

function basenameNoExt (string $path)
{
    return pathinfo ($path, PATHINFO_FILENAME);
}

function file_ext (string $path)
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

function explode2 (string $separator, string $string, ...$limit)
{ 
    return strlen ($string) ?
        explode ($separator, $string, ...$limit) :
        array ();
}

function substr_icount (string $haystack, string $needle, ...$params)
{ 
	return substr_count (strtolower ($haystack), strtolower ($needle), ...$params);
}

function str_replace_assoc (string $subject, array $replacements)
{
	return str_replace (array_keys ($replacements), array_values ($replacements), $subject);
}

function pre (...$args)
{
	if (sizeof ($args) < 2 )
		$args = current ($args);
	
	message (print_r ($args, true));
}

function setTimer (int $interval, $function)
{
	$timer           = new Timer;
    $timer->interval = $interval;
    
    $timer->TickEvent = function ($self) use ($function)
    {
        is_callable ($function) ?
            call_user_func ($function, $self) :
            eval ($function);
    };
    
	$timer->start ();
    
    return $timer;
}

function setTimeout (int $interval, $function)
{
	$timer           = new Timer;
    $timer->interval = $interval;
    
    $timer->TickEvent = function ($self) use ($function)
    {
        is_callable ($function) ?
            call_user_func ($function, $self) :
            eval ($function);

        $self->dispose ();
    };
    
    $timer->start ();
    
	return $timer;
}

function closure_add (string $args, string $code)
{
	return eval ("return function ($args) {$code};");
}

function function_add (string $functionName, string $args, string $code)
{
	eval ("function $functionName ($args) {$code}");
}

function includeComponent (string $componentName)
{
    if (!class_exists ($componentName) && file_exists (dirname (__DIR__) ."/components/$componentName.php"))
        require_once dirname (__DIR__) ."/components/$componentName.php";
}

function getLogicalVarType ($data)
{
    if (is_object ($data))
        return 'object';

    elseif (is_callable ($data))
        return 'callable';

    elseif (is_array ($data))
        return 'array';

    elseif (is_bool ($data))
        return 'bool';

    elseif (is_double ($data))
        return 'double';

    elseif (is_numeric ($data))
        return 'int';

    else return 'string';
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
                return VoidEngine::getProperty ($this->selector, 'Count', 'int');
            break;
				
            case 'list':
                $size = VoidEngine::getProperty ($this->selector, 'Count', 'int');
                $list = [];
                
				for ($i = 0; $i < $size; ++$i)
                    $list[] = VoidEngine::getArrayValue ($this->selector, $i, 'string');
                    
                return $list;
            break;
		}
    }
	
	public function add ($value)
	{
		return $this->offsetSet (null, $value);
	}
	
	public function append ($value)
	{
		return $this->offsetSet (null, $value);
	}
	
	public function offsetSet ($index, $value)
	{
        return $index === null ?
            VoidEngine::callMethod ($this->selector, 'Add', '', $value, 'string') :
            VoidEngine::callMethod ($this->selector, 'Insert', '', (int) $index, 'int', $value, 'string');
	}
	
	public function offsetGet ($index)
	{
		return VoidEngine::getArrayValue ($this->selector, (int) $index, 'string');
	}
	
	public function addRange (array $items)
	{
		array_map ([$this, 'append'], $items);
	}
	
	public function offsetUnset ($index)
	{
		VoidEngine::callMethod ($this->selector, 'RemoveAt', '', (int) $index, 'int');
	}
	
	public function remove ($index)
	{
		$this->offsetUnset ($index);
	}
	
	public function clear ()
	{
		VoidEngine::callMethod ($this->selector, 'Clear');
	}
	
	public function indexOf (string $value)
	{
		return VoidEngine::getProperty ($this->selector, 'IndexOf', 'int', $value, 'string');
	}
	
	public function insert ($index, $value)
	{
		return $this->offsetSet ($index, $value);
	}
	
	public function contains (string $value)
	{
		return VoidEngine::getProperty ($this->selector, 'Contains', 'bool', $value, 'string');
	}
}

class Icon
{
    protected $selector;

    public function __construct (string $file)
    {
        $icon = new WFObject ('System.Drawing.Icon', 'System.Drawing');
        $icon->token = 'b03f5f7f11d50a3a';

		$this->selector = VoidEngine::createObject ($icon, $file, 'string');
    }

    public function applyToObject (int $selector)
	{
		VoidEngine::setProperty ($selector, 'Icon', $this->selector, 'object');
	}
	
	public function saveToFile (string $file)
	{
		VoidEngine::callMethod ($this->selector, 'Save', '', $file, 'string');
	}
}

class Cursor
{
    protected $cursor;

    public function __construct (int $handle = null)
    {
        $cursor = new WFObject ('System.Windows.Forms.Cursor', 'System.Windows.Forms');

        $this->cursor = $handle === null ?
            VoidEngine::buildObject ($cursor) :
            VoidEngine::createObject ($cursor, $handle, 'handle');
    }

    public function getPosition ()
    {
        $pos = VoidEngine::getProperty ($this->cursor, 'Position', 'object');

        return [
            VoidEngine::getProperty ($pos, 'X', 'int'),
            VoidEngine::getProperty ($pos, 'Y', 'int')
        ];
    }
}

function get_cursor_x (int $handle = null)
{
    $cursor = new Cursor ($handle);

    return $cursor->getPosition ()[0];
}

function get_cursor_y (int $handle = null)
{
    $cursor = new Cursor ($handle);

    return $cursor->getPosition ()[1];
}

// set_logmessage_handler ('pre');

set_error_handler (function ($errno, $errstr = '', $errfile = '', $errline = '', $errcontext = '')
{
    file_put_contents (dirname (__DIR__) .'/debug/error_'. (++$GLOBALS['__debug']['error_count']) .'.log', implode ("\n", [
        'Time lapsed before engine start: '. (string)(round ((microtime (true) - $GLOBALS['__debug']['start_time']) / 1000, 2)) .' seconds',
        'Error at string: '. $errstr,
        'Error in file: '. $errfile,
        'Error at line: '. $errline,
        'Error context: '. json_encode ($errcontext),
        'Created components: '. print_r (Components::$components, true)
    ]));

    $log = 'New error catched as "error_'. $GLOBALS['__debug']['error_count'] .'.log"';

    VoidStudioAPI::getObjects ('main')['Log_List']->items->add ('[!]'. $log);
    pre ($log);
});

set_exception_handler (function ($exception)
{
    file_put_contents (dirname (__DIR__) .'/debug/exception_'. (++$GLOBALS['__debug']['error_count']) .'.log', implode ("\n", [
        'Time lapsed before engine start: '. (string)(round ((microtime (true) - $GLOBALS['__debug']['start_time']) / 1000, 2)) .' seconds',
        'Exception comment: '. $exception,
        'Created components: '. print_r (Components::$components, true)
    ]));

    $log = 'New exception catched as "exception_'. $GLOBALS['__debug']['error_count'] .'.log"';

    VoidStudioAPI::getObjects ('main')['Log_List']->items->add ('[!]'. $log);
    pre ($log);
});

?>
