<?php

/**
 * @package VLF (Void Language File)
 * Формат файлов для разметки приложений на VoidEngine
 * 
 * Документация:
 * @see <https://vk.com/@winforms-vlf-dlya-chainikov>
 */

namespace VoidEngine;

class VLFReader
{
    static $objects    = [];
    static $obj        = '';
    static $skipAt     = -1;
    static $globalCode = '';

    /**
     * * Выполнение VLF разметки
     * 
     * @var string file - путь до файла с VLF разметкой или сама VLF разметка
     * 
     * @return array objects - возвращает массив созданных объектов (название => объект)
     */
    
    static function read (string $file): array
    {
        if (file_exists ($file))
            $file = file_get_contents ($file);

        $file = explode ("\n", $file);

        $objects    = self::$objects;
        $obj        = self::$obj;
        $skipAt     = self::$skipAt;
        $globalCode = self::$globalCode;

        self::$objects    = [];
        self::$obj        = '';
        self::$skipAt     = -1;
        self::$globalCode = '';

        foreach ($file as $id => $line)
        {
            if ($id < $skipAt || !self::isReadable ($line))
                continue;

            $check = ($line[0] != ' ' && $line[0] != "\t");
            $line  = explode (' ', trim ($line));
            $base  = trim ($line[0]);
            $other = join (' ', array_slice ($line, 1));

            if ($base[0] == '#')
            {
                if (isset ($base[1]) && $base[1] == '^')
                {
                    $step = self::getLineHard ($file[$id]);

                    for ($i = $id + 1; isset ($file[$i]); ++$i)
                        if (self::isReadable ($file[$i]))
                            if (self::getLineHard ($file[$i]) <= $step)
                                break;

                    $skipAt = $i;
                }

                continue;
            }

            elseif ($base[0] == '*' || $base[0] == '%')
            {
                $data = substr (trim ($file[$id]), 1);

                if (isset ($base[1]) && $base[1] == '^')
                {
                    $data = substr ($data, 1);
                    $step = self::getLineHard ($file[$id]);

                    for ($i = $id + 1; isset ($file[$i]); ++$i)
                        if (self::isReadable ($file[$i]))
                        {
                            if (self::getLineHard ($file[$i]) > $step)
                                $data .= substr ($file[$i], $step);

                            else break;
                        }

                    $data   = trim ($data);
                    $skipAt = $i;
                }

                $data = self::formatLine ($data, $objects);
                
                $base[0] == '%' ?
                    eval ($data) :
                    $globalCode .= "$data\n";
            }

            elseif ($check)
            {
                $name = trim (
                    ($pos = strpos ($other, '(')) !== false ?
                    substr ($other, 0, $pos) : $other
                );

                $obj     = $name;
                $content = false;

                if (isset ($objects[$name]))
                    continue;

                $obj_name = "base64_decode ('". base64_encode ($name) ."')";
                
                if (($begin = strpos ($other, '(')) !== false && ($end = strrpos ($other, ')')) !== false)
                    $content = '('. self::formatLine (substr ($other, $begin + 1, $end - $begin - 1), $objects) .')';

                includeComponent ($base);
                $objects[$name] = eval ("namespace VoidEngine; $globalCode \$_obj = new $base $content; if (property_exists (\$_obj, 'name')) \$_obj->name = $obj_name; elseif (method_exists (\$_obj, 'set_name')) \$_obj->name = $obj_name; return \$_obj;");
            }

            elseif (substr ($base, 0, 2) == '->')
            {
                $method = join (' ', $line);
                $args   = substr ($method, ($pos = strpos ($method, '(')), strrpos ($method, ')') - $pos + 1);
                $method = str_replace ($args, self::formatLine ($args, $objects), $method);

                eval ("namespace VoidEngine; $globalCode Components::getComponent ('". $objects[$obj]->selector ."')$method;");
            }

            else
            {
                $end = substr ($base, strlen ($base) - 1);

                if ($end != ':' && $end != '^')
                {
                    $data = trim ($file[$id]);

                    if (($begin = strpos ($data, '(')) !== false && ($end = strrpos ($data, ')')) !== false)
                    {
                        ++$begin;
                        $end -= $begin;

                        $haveArguments = strlen (trim (substr ($data, $begin, $end))) > 0;

                        /**
                         * Пока что сделал так, что если аргументов нету или они пустые, то достроится аргумент - ссылка на объект-родитель
                         * 
                         * К примеру:
                         * 
                         * Form MainForm
                         *      Button MainButton
                         *          caption: 'test'
                         * 
                         * достроит MainButton до MainButton (MainForm)
                         * 
                         * а вот
                         * 
                         * Form SecondForm
                         * Form MainForm
                         *      Button MainButton (SecondForm):
                         *          caption: 'test'
                         * 
                         * оставит как есть
                         */
                        
                        if (!$haveArguments)
                        {
                            $post = $haveArguments ?
                                "$obj, " : $obj;

                            /*if ($haveArguments)
                            {
                                $edge = strpos ($data, ',');

                                $edge = $edge === false ?
                                    $end : $edge - $begin;
                                
                                $firstArgument = substr ($data, $begin, $edge);

                                if ($firstArgument == $obj)
                                    $post = '';
                            }*/

                            $data = substr ($data, 0, $begin) . $post . substr ($data, $begin);
                        }
                    }

                    else $data .= " ($obj)";

                    $step = self::getLineHard ($file[$id]);
                    $data .= "\n";

                    for ($i = $id + 1; isset ($file[$i]); ++$i)
                        if (self::isReadable ($file[$i]))
                        {
                            if (self::getLineHard ($file[$i]) > $step)
                                $data .= substr ($file[$i], $step) ."\n";

                            else break;
                        }

                    $data   = trim ($data);
                    $skipAt = $i;

                    self::$objects    = $objects;
                    self::$obj        = $obj;
                    self::$globalCode = $globalCode;

                    $objects = array_merge ($objects, self::read ($data));
                }

                else
                {
                    $method = substr ($base, 0, -1);
                    $preset = '';

                    if (substr ($base, strlen ($base) - 1) == '^')
                    {
                        $data = $other;
                        $step = self::getLineHard ($file[$id]);

                        for ($i = $id + 1; isset ($file[$i]); ++$i)
                            if (self::isReadable ($file[$i]))
                            {
                                if (self::getLineHard ($file[$i]) > $step)
                                    $data .= substr ($file[$i], $step);

                                else break;
                            }

                        $method = substr ($method, 0, -1);
                        $other  = trim ($data);
                        $skipAt = $i;
                    }

                    if (preg_match ('/function \((.*)\) use \((.*)\)/', $other))
                    {
                        $use = substr ($other, strpos ($other, 'use'));
                        $use = $ouse = substr ($use, ($pos = strpos ($use, '(') + 1), strpos ($use, ')') - $pos);
                        $use = explode (' ', $use);

                        foreach ($use as $id => $useParam)  
                            if (isset ($objects[$useParam]) && $use[$id + 1][0] == '$')
                            {
                                $fname = $use[$id + 1];

                                if (substr ($fname, strlen ($fname) - 1) == ',')
                                    $fname = substr ($fname, 0, -1);

                                $preset .= "$fname = $useParam; ";

                                unset ($use[$id]);
                            }

                        $preset = self::formatLine ($preset, $objects);
                        $other  = str_replace ($ouse, join (' ', $use), $other);
                    }

                    $other = self::formatLine ($other, $objects);
                    $objects[$obj]->$method = eval ("namespace VoidEngine; $globalCode $preset return $other;");
                }
            }
        }

        return $objects;
    }

    static function formatLine ($line, array $objects = []): string
    {
        if (sizeof ($objects) > 0)
        {
            $len     = strlen ($line);
            $newLine = '';

            $replacement = array_map (function ($object)
            {
                return $object instanceof Control ? 
                    '\VoidEngine\Components::getComponent (\''. $object->selector .'\')' :
                    'unserialize (\''. serialize ($object) .'\')';
            }, $objects);

            $replacement = array_flip ($replacement);
            arsort ($replacement);
            $replacement = array_flip ($replacement);

            $blacklist = array_flip (['\'', '"', '$']);

            for ($i = 0; $i < $len; ++$i)
            {
                $replaced = false;

                foreach ($replacement as $name => $replaceAt)
                    if (substr ($line, $i, ($l = strlen ($name))) == $name && !isset ($blacklist[$line[$i - 1]]))
                    {
                        $newLine .= $replaceAt;

                        $i += $l - 1;
                        $replaced = true;

                        break;
                    }

                if (!$replaced)
                    $newLine .= $line[$i];
            }

            $line = $newLine;
        }

        return $line;
    }

    static function filterArray (array $array): array
    {
        return array_filter ($array, function ($line)
        {
            return VLFReader::isReadable ($line);
        });
    }

    static function isReadable (string $line): bool
    {
        return (bool)(trim ($line)); 
    }

    static function getLineHard (string $line): int
    {
        /*$line = str_replace ("\t", ' ', $line);
        $size = strlen ($line);

        for ($i = 0; $i < $size; ++$i)
            if ($line[$i] != ' ')
                break;

        return $i;*/
		
		return strlen ($line) - strlen (ltrim ($line));
    }
}

?>
