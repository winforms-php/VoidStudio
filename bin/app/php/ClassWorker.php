<?php

namespace VoidEngine;

class ClassWorker
{
    public static function applyClass (string $code, string $class, string $apply): string
    {
        $code = self::stripComments ($code);

        $split1 = $split2 = false;

        $len      = strlen ($code);
        $classLen = strlen ($class);

        $class_predefined = false;
        $class_close = null;

        for ($i = 0; $i < $len; ++$i)
        {
            if ($code[$i] == '\'' && !$split2)
                $split1 = !$split1;

            elseif ($code[$i] == '"' && !$split1)
                $split2 = !$split2;

            elseif (!$split1 && !$split2)
            {
                if ($code[$i] == 'c' && substr ($code, $i, 5) == 'class')
                {
                    for ($j = $i + 5; $j < $len; ++$j)
                        if (in_array ($code[$j], ["\n", "\r", "\t", ' ']))
                            continue;

                        else
                        {
                            if (substr ($code, $j, $classLen) == $class)
                                $class_predefined = true;

                            $i = $j;

                            break;
                        }
                }

                elseif ($class_predefined == true)
                {
                    if ($code[$i] == '{')
                    {
                        if ($class_close === null)
                            $class_close = 1;

                        else ++$class_close;
                    }

                    elseif ($code[$i] == '}')
                        --$class_close;

                    if ($class_close === 0)
                        return substr ($code, 0, $i) . $apply . substr ($code, $i);
                }
            }
        }

        return $code;
    }

    public static function getAvailableClassMethods (string $code, string $class): array
    {
        $code = self::stripComments ($code);

        $split1 = $split2 = false;

        $len      = strlen ($code);
        $classLen = strlen ($class);

        $class_predefined = false;
        $class_close = null;

        $methods = [];

        for ($i = 0; $i < $len; ++$i)
        {
            if ($code[$i] == '\'' && !$split2)
                $split1 = !$split1;

            elseif ($code[$i] == '"' && !$split1)
                $split2 = !$split2;

            elseif (!$split1 && !$split2)
            {
                if ($code[$i] == 'c' && substr ($code, $i, 5) == 'class')
                {
                    for ($j = $i + 5; $j < $len; ++$j)
                        if (in_array ($code[$j], ["\n", "\r", "\t", ' ']))
                            continue;

                        else
                        {
                            if (substr ($code, $j, $classLen) == $class)
                                $class_predefined = true;

                            $i = $j;

                            break;
                        }
                }

                elseif ($class_predefined == true)
                {
                    if ($code[$i] == 's' && substr ($code, $i, 6) == 'static')
                    {
                        for ($j = $i + 6; $j < $len; ++$j)
                            if (!in_array ($code[$j], ["\n", "\r", "\t", ' ']))
                                break;

                        if ($code[$j] == 'f' && substr ($code, $j, 8) == 'function')
                        {
                            for ($j = $j + 8; $j < $len; ++$j)
                                if (in_array ($code[$j], ["\n", "\r", "\t", ' ']))
                                    continue;

                                else
                                {
                                    $i = $j;
                                    $methods[] = trim (substr ($code, $j, strpos ($code, '(', $j) - $j));

                                    break;
                                }
                        }
                    }
                    
                    elseif ($code[$i] == '{')
                    {
                        if ($class_close === null)
                            $class_close = 1;

                        else ++$class_close;
                    }

                    elseif ($code[$i] == '}')
                        --$class_close;

                    if ($class_close === 0)
                        return $methods;
                }
            }
        }

        return $methods;
    }

    public static function stripComments (string $code): string
    {
        $tokens = token_get_all ("<?php\n\n". $code);
        $return = '';

        foreach ($tokens as $token)
            if (is_string ($token))
                $return .= $token;

            else
            {
                list ($id, $text) = $token;

                switch ($id)
                {
                    case T_COMMENT:
                    case T_DOC_COMMENT:
                        break;

                    default:
                        $return .= $text;

                        break;
                }
            }

        return substr ($return, 7);
    }
}
