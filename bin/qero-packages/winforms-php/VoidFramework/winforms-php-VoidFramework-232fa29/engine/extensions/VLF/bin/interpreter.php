<?php

namespace VoidEngine;

class VLFInterpreter
{
    static $objects = []; // Массив созданных объектов (название => объект)

    static $throw_errors = true; // Выводить ли ошибки интерпретации
    static $allow_multimethods_calls = true; // Можно ли использовать многоуровневые вызовы методов (->method1->method2)

    /**
     * * Интерпретирование синтаксического дерева
     * Выполняет то, что было сгенерировано парсером VLF кода
     * 
     * @param mixed $syntaxTree - Абстрактное Синтаксическое Дерево (АСД), сгенерированное VLFParser'ом, или сам VLFParser
     * [@param string $resourceDir = null] - директория с ресурсами для импорта
     * [@param array $parent = null] - нода-родитель дерева (системная настройка)
     * 
     * @return array - возвращает список созданных объектов
     */

    public static function run ($syntaxTree, string $resourcesDir = null, array $parent = null): array
    {
        if ($syntaxTree instanceof VLFParser)
            $syntaxTree = $syntaxTree->tree;

        elseif (!is_array ($syntaxTree) && self::$throw_errors)
            throw new \Exception ('$syntaxTree argument must be instance of VoidEngine\VLFParser or contains Abstract Syntax Tree - multi-dimensional array');

        foreach ($syntaxTree as $id => $syntaxInfo)
            if (isset ($syntaxInfo['type']))
            {
                switch ($syntaxInfo['type'])
                {
                    case VLF_OBJECT_DEFINITION:
                        $class = $syntaxInfo['info']['object_class'];
                        $name  = $syntaxInfo['info']['object_name'];
                        $args  = [];

                        if (isset ($syntaxInfo['info']['arguments']))
                        {
                            $args = $syntaxInfo['info']['arguments'];

                            foreach ($args as $arg_id => $arg)
                                if (is_object ($arg) && $arg instanceof VLFLink)
                                    $args[$arg_id] = isset (self::$objects[$arg->name]) ?
                                        self::formatLine ($arg->name, self::$objects) : null;

                                else $args[$arg_id] = self::formatLine ($arg, self::$objects);
                        }

                        try
                        {
                            self::$objects[$name] = eval ("namespace VoidEngine; return new $class (". implode (', ', $args) .");");

                            try
                            {
                                self::$objects[$name]->name = $name;
                            }

                            catch (\Throwable $e) {}
                        }

                        catch (\Throwable $e)
                        {
                            if (self::$throw_errors)
                                throw new \Exception ('Interpeter couldn\'t create object "'. $class .'" with name "'. $name .'" at line "'. $syntaxInfo['line'] .'". Exception info:'. "\n\n". (string) $e, 0, $e);
                        }
                    break;

                    case VLF_SUBOBJECT_DEFINITION:
                        self::$objects = self::run ((new VLFParser ($syntaxInfo['info']['object_vlf_info']))->tree, null, $syntaxInfo);
                    break;

                    case VLF_PROPERTY_SET:
                        if (isset ($parent['info']['object_name']) && isset (self::$objects[$name = $parent['info']['object_name']]))
                        {
                            $propertyName  = $syntaxInfo['info']['property_name'];
                            $propertyValue = $syntaxInfo['info']['property_value'];
                            $preset        = '';

                            if (is_object ($propertyValue) && $propertyValue instanceof VLFLink)
                                $propertyValue = isset (self::$objects[$propertyValue->name]) ?
                                    self::formatLine ($propertyValue->name, self::$objects) : null;

                            elseif (preg_match ('/function \((.*)\) use \((.*)\)/', $propertyValue))
                            {
                                $use = substr ($propertyValue, strpos ($propertyValue, 'use'));
                                $use = $ouse = substr ($use, ($pos = strpos ($use, '(') + 1), strpos ($use, ')') - $pos);
                                $use = explode (' ', $use);

                                foreach ($use as $id => $useParam)  
                                    if (isset (self::$objects[$useParam]) && $use[$id + 1][0] == '$')
                                    {
                                        $fname = $use[$id + 1];

                                        if (substr ($fname, strlen ($fname) - 1) == ',')
                                            $fname = substr ($fname, 0, -1);

                                        $preset .= "$fname = $useParam; ";

                                        unset ($use[$id]);
                                    }

                                $preset        = self::formatLine ($preset, self::$objects);
                                $propertyValue = self::formatLine (str_replace ($ouse, join (' ', $use), $propertyValue), self::$objects);
                            }

                            else $propertyValue = self::formatLine ($propertyValue, self::$objects);

                            try
                            {
                                self::$objects[$name]->$propertyName = eval ("namespace VoidEngine; $preset return $propertyValue;");
                            }

                            catch (\Throwable $e)
                            {
                                try
                                {
                                    $propertyValue = $syntaxInfo['info']['property_raw_value'];

                                    if (strpos ($propertyName, '->') !== false)
                                        eval ('namespace VoidEngine; '. $preset .' _c('. self::$objects[$name]->selector .')->'. $propertyName .' = '. $propertyValue .';');

                                    else self::$objects[$name]->$propertyName = eval ("namespace VoidEngine; $preset return $propertyValue;");
                                }

                                catch (\Throwable $e)
                                {
                                    if (self::$throw_errors)
                                        throw new \Exception ('Interpeter couldn\'t set property "'. $propertyName .'" with value "'. $propertyValue .'" at line "'. $syntaxInfo['line'] .'". Exception info:'. "\n\n". (string) $e, 0, $e);
                                }
                            }
                        }

                        elseif (self::$throw_errors)
                            throw new \Exception ('Setting property to an non-object at line "'. $syntaxInfo['line']);
                    break;

                    case VLF_METHOD_CALL:
                        if (isset ($parent['info']['object_name']) && isset (self::$objects[$name = $parent['info']['object_name']]))
                        {
                            $methodName = $syntaxInfo['info']['method_name'];
                            $methodArgs = $syntaxInfo['info']['method_arguments'];

                            foreach ($methodArgs as $arg_id => $arg)
                                if (is_object ($arg) && $arg instanceof VLFLink)
                                    $methodArgs[$arg_id] = isset (self::$objects[$arg->name]) ?
                                        self::formatLine ($arg->name, self::$objects) : null;

                                else $methodArgs[$arg_id] = self::formatLine ($arg, self::$objects);

                            try
                            {
                                if (strpos ($methodName, '->') !== false && self::$allow_multimethods_calls)
                                    eval ('namespace VoidEngine; _c('. self::$objects[$name]->selector .')->'. $methodName .' ('. implode (', ', $methodArgs) .');');

                                else self::$objects[$name]->$methodName (...$methodArgs);
                            }

                            catch (\Throwable $e)
                            {
                                if (self::$throw_errors)
                                    throw new \Exception ('Interpeter couldn\'t call method "'. $methodName .'" with arguments '. json_encode ($methodArgs) .' at line "'. $syntaxInfo['line'] .'". Exception info:'. "\n\n". (string) $e, 0, $e);
                            }
                        }

                        elseif (self::$throw_errors)
                            throw new \Exception ('Calling method to an non-object at line "'. $syntaxInfo['line'] .'"');
                    break;

                    case VLF_RUNTIME_EXECUTABLE:
                        eval (self::formatLine ($syntaxInfo['info']['code'], self::$objects));
                    break;
                }

                if (isset ($syntaxInfo['syntax_nodes']) && sizeof ($syntaxInfo['syntax_nodes']) > 0)
                    self::$objects = self::run ($syntaxInfo['syntax_nodes'], null, $syntaxInfo);
            }

            else throw new \Exception ('Catched unknown syntax node: "'. json_encode ($syntaxInfo) .'"');

        if (is_dir ($resourcesDir))
            foreach (glob ($resourcesDir .'/*.vrsf') as $id => $dir)
            {
                $baseName = basenameNoExt ($dir);
                $info     = explode ('.', $baseName);

                if (isset (self::$objects[$info[0]]))
                {
                    if (isset ($info[2]))
                    {
                        $collection = VoidEngine::getProperty (self::$objects[$info[0]]->selector, $info[1]);
                        
                        VoidEngine::callMethod ($collection, 'Add', [VoidEngine::importObject (base64_encode (file_get_contents ($dir))), 'object']);
                    }
                    
                    else VoidEngine::setProperty (self::$objects[$info[0]]->selector, $info[1], VoidEngine::importObject (base64_encode (file_get_contents ($dir))));
                }
            }

        return self::$objects;
    }

    /**
     * * Форматирование строки
     * Необходимо для замены ссылок на объекты из человекочитаемого вида на PHP код
     * 
     * @param string $line - строка для форматирования
     * [@param array $objects = []] - список объектов, которые будут участвовать в форматировании
     * 
     * @return string - возвращает форматированную строку
     */

    public static function formatLine (string $line, array $objects = []): string
    {
        if (sizeof ($objects) > 0)
        {
            $len     = strlen ($line);
            $newLine = '';

            $replacement = array_map (function ($object)
            {
                return Components::componentExists ($object->selector) !== false ? 
                    '\VoidEngine\_c('. $object->selector .')' :
                    'unserialize (\''. serialize ($object) .'\')';
            }, $objects);

            $replacement = array_map (function ($name)
            {
                return strlen ($name = trim ($name)) + substr_count ($name, '_');
            }, $omap = array_flip ($replacement));

            arsort ($replacement);

            $nReplacement = [];

            foreach ($replacement as $replaceTo => $nLn)
                $nReplacement[$omap[$replaceTo]] = $replaceTo;

            $replacement = $nReplacement;
            $blacklist   = array_flip (['\'', '"', '$']);

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
}
