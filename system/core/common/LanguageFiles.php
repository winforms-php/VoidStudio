<?php

/*
    Класс для чтения VLF (Void Language File)-файлов
    VLF-файлы - файлы синтаксиса, созданные для упрощённой разработки на VoidEngine
*/

namespace VoidEngine;

class VLFReader
{
    static function read (string $file)
    {
        if (file_exists ($file))
            $file = file_get_contents ($file);

        $file = explode ("\n", $file);

        $objects    = [];
        $obj        = '';
        $skipAt     = -1;
        $globalCode = '';

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
                if ($base[1] == '^')
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

                if ($base[1] == '^')
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

                $obj = $name;

                if (isset ($objects[$name]))
                    continue;

                $content = substr ($other, ($pos = strpos ($other, '(') + 1), strrpos ($other, ')') - $pos);
                $content = self::formatLine ($content, $objects);

                includeComponent ($base);
                $objects[$name] = eval ("namespace VoidEngine; $globalCode return new $base ($content);");
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

        return $objects;
    }

    static function formatLine ($line, array $objects = [])
    {
        if (sizeof ($objects) > 0)
        {
            $len     = strlen ($line);
            $newLine = '';

            $replacement = array_map (function ($object)
            {
                return (
                    $object instanceof Control ? 
                        '\VoidEngine\Components::getComponent (\''. $object->selector .'\')' :
                        'unserialize (\''. serialize ($object) .'\')'
                );
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

    static function filterArray (array $array)
    {
        return array_filter ($array, function ($line)
        {
            return VLFReader::isReadable ($line);
        });
    }

    static function isReadable (string $line)
    {
        return (bool)(trim ($line)); 
    }

    static function getLineHard (string $line)
    {
        $line = str_replace ("\t", ' ', $line);
        $size = strlen ($line);

        for ($i = 0; $i < $size; ++$i)
            if ($line[$i] != ' ')
                break;

        return $i;
    }
}

?>
