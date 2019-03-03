<?php

namespace VoidEngine;

class VLFParser
{
    public $divider = "\n"; // Разделитель строк

    public $strong_line_parser            = true; // Использовать ли строгий парсер слов (только алфавит и цифры)
    public $ignore_postobject_info        = false; // Игнорировать ли символы после скобок объектов
    public $ignore_unexpected_method_args = false; // Игнорировать ли отсутствующие перечисления аргументов методов

    public $use_caching = false; // Кэшировать ли деревья
    public $debug_mode  = false; // Выводить ли дебаг-сообщения парсера

    protected $tree; // АСД (Абстрактное Синтаксическое Дерево)
    protected $links; // Список ссылок объект -> индекс в АСД

    /**
     * * Конструктор парсера
     * Выполняет парсинг АСД из VLF разметки, если надо - кэширует его
     * 
     * @param string $content - VLF разметка или путь до файла разметки
     * [@param array $settings = []] - список настроек и их значений (настройка => значение)
     * 
     */

    public function __construct (string $content, array $settings = [])
    {
        if (file_exists ($content))
            $content = file_get_contents ($content);

        // Зачем? Так надо!
        $content = "# VLF begin\n\n$content\n\n# VLF end";

        foreach ($settings as $name => $setting)
        {
            if (isset ($this->$name))
                $this->$name = $setting;

            else throw new \Exception ('Trying to setting up undefined property "'. $name .'"');
        }

        if ($this->use_caching && file_exists ($file = text (VLF_EXT_DIR .'/cache/'. sha1 ($content) .'.cache')))
        {
            $info = unserialize (gzinflate (file_get_contents ($file)));

            if ($info[0] == sha1 (file_get_contents (text (__FILE__))))
            {
                $this->tree  = $info[1][0];
                $this->links = $info[1][1];

                return;
            }

            else unlink ($file);
        }

        $info = $this->generateSyntaxTree ($content);

        $this->tree  = $info[0];
        $this->links = $info[1];

        if ($this->use_caching)
            file_put_contents (text (VLF_EXT_DIR .'/cache/'. sha1 ($content) .'.cache'), gzdeflate (serialize ([sha1 (file_get_contents (text (__FILE__))), $info])));
    }

    /**
     * * Генератор АСД
     * Конвертирует VLF разметку в АСД
     * 
     * @param string $content - VLF разметка
     * 
     * @return array - возвращает АСД
     * 
     */

    protected function generateSyntaxTree (string $content): array
    {
        $lines          = $this->linesFilter ($untouched_lines = explode ($this->divider, $content));
        $current_object = null;
        $parent_objects = [];
        $skip_at        = -1;
        $tree           = [];
        $links          = [];

        if ($this->debug_mode)
            pre ($lines);

        foreach ($lines as $id => $line)
        {
            if ($skip_at > $id)
                continue;

            $height = $this->getLineHeight ($line);
            $words  = $this->linesFilter (explode (' ', $line));

            if ($this->debug_mode)
                pre ($words);

            /**
             * Высокоинтеллектуальный фикс
             * Редирект из объектов более высокого уровня к более низкому уровню
             * 
             * Form MainForm
             *     Button MainButton1
             *         ...
             * 
             *     caption: 'MainForm'
             * 
             * Нужно для того, что-бы указатель с объекта MainButton1 спрыгнул обратно на MainForm
             * 
             * subparent_link нужен цикл while для того, что-бы перебрать некоторые подобъекты, у которых в аргументах
             * не используются ссылки на оригиналы объектов
             * 
             */

            while ($current_object !== null && $tree[$current_object]['hard'] >= $height)
            {
                $updated = false;

                if ($this->debug_mode)
                    pre ($current_object);

                while (isset ($tree[$current_object]['info']['subparent_link']) && $tree[$link = $tree[$current_object]['info']['subparent_link']->link]['hard'] < $tree[$current_object]['hard'])
                {
                    $current_object = $link;
                    $updated        = true;

                    if ($this->debug_mode)
                        pre ($current_object);
                }

                if (
                    !$updated &&
                    isset ($tree[$current_object]['info']['arguments']) &&
                    isset ($tree[$current_object]['info']['arguments'][0]) &&
                    $tree[$current_object]['info']['arguments'][0] instanceof VLFLink &&
                    $tree[$tree[$current_object]['info']['arguments'][0]->link]['hard'] < $tree[$current_object]['hard']
                ) $current_object = $tree[$current_object]['info']['arguments'][0]->link;

                elseif (!$updated)
                    break;

                if ($this->debug_mode)
                    pre ($current_object);
            }

            /**
             * Button ...
             */

            if (class_exists ($words[0]) || class_exists ('\VoidEngine\\'. $words[0]))
            {
                if (!isset ($words[1]))
                    throw new \Exception ('Object name mustn\'t be empty at line "'. $line .'"');

                /**
                 * Button NewButton
                 * ...
                 * 
                 * Button NewButton
                 *     text: 123
                 */
                
                if (isset ($links[$words[1]]))
                {
                    $tree[$id] = [
                        'type'  => VLF_OBJECT_REDIRECTION,
                        'line'  => $line,
                        'hard'  => $height,
                        'words' => $words,

                        'info' => [
                            'object_class' => $words[0],
                            'object_name'  => $words[1]
                        ],

                        'syntax_nodes' => []
                    ];

                    $current_object = $id;

                    continue;
                }

                else
                {
                    $tree[$id] = [
                        'type'  => VLF_OBJECT_DEFINITION,
                        'line'  => $line,
                        'hard'  => $height,
                        'words' => $words,

                        'info' => [
                            'object_class' => $words[0],
                            'object_name'  => $words[1]
                        ],

                        'syntax_nodes' => []
                    ];

                    if (($begin = strpos ($line, '(')) !== false)
                    {
                        ++$begin;
                        
                        $end = strrpos ($line, ')');

                        if ($end === false)
                            throw new \Exception ('Line "'. $line .'" have arguments list initialization, but not have list ending');

                        elseif ($begin < $end)
                        {
                            $arguments = [];
                            $parsed    = explode (',', substr ($line, $begin, $end - $begin));

                            foreach ($parsed as $argument_id => $argument)
                            {
                                $argument = trim ($argument);

                                if (strlen ($argument) > 0)
                                    $arguments[] = isset ($links[$argument]) ?
                                        new VLFLink ($argument, $links[$argument]) :
                                        $argument;

                                else throw new \Exception ('Argument '. ($argument_id + 1) .' mustn\'t have zero length at line "'. $line .'"');
                            }

                            $tree[$id]['info']['arguments'] = $arguments;

                            if (!$this->ignore_postobject_info && trim (substr ($line, $end)) > 0)
                                throw new \Exception ('You mustn\'t write any chars after arguments definition');
                        }

                        $tree[$id]['info']['subparent_link'] = new VLFLink ($tree[$current_object]['info']['object_name'], $current_object);
                    }

                    /**
                     * Form MainForm
                     *     Button MainButton
                     */

                    elseif ($current_object !== null && $tree[$current_object]['hard'] < $height)
                    {
                        $tree[$id]['info']['arguments'] = [
                            new VLFLink ($tree[$current_object]['info']['object_name'], $current_object)
                        ];

                        $parent_objects[$id] = $current_object;
                    }

                    /**
                     * Если высота блока будет выше, чем высота текущего объекта, то текущий объект будет обработан кодом выше
                     * Если высота блока будет ниже, чем высота текущего объекта, то он создан вне блока текущего объекта и вообще не обрабатывается
                     * Если же высоты совпадают, то мы дописываем текущему объекту в аргументы 
                     * 
                     * ? Вариант с одинаковыми высотами временно отключен -_-
                     */

                    /*elseif (
                        $current_object !== null &&
                        $tree[$current_object]['hard'] == $height
                    )
                    {
                        $tree[$id]['info']['arguments'] = [
                            new VLFLink ($tree[$current_object]['info']['object_name'], $current_object)
                        ];

                        $parent_objects[$id] = $current_object;
                    }*/

                    $links[$tree[$id]['info']['object_name']] = $id;
                    $current_object = $id;
                }
            }

            /**
             * # ALALALAHAAHAH
             * 
             * #^ ALALA
             *    HAHAHA
             * 
             *    SUPER COMMENT 3000
             */

            elseif ($words[0][0] == '#')
            {
                $comment = $line;

                if (isset ($words[0][1]))
                {
                    if ($words[0][1] == '^')
                    {
                        $parsed = $this->parseSubText ($untouched_lines, $id, $height);

                        $comment .= $parsed[0];
                        $skip_at  = $parsed[1];
                    }

                    else throw new \Exception ('Unknown char founded after syntax-control symbol at line "'. $line .'"');
                }
                
                if ($this->debug_mode)
                    pre ("Comment:\n\n$comment");
            }

            /**
             * % VoidEngine\pre (123);
             * 
             * % namespace VoidEngine;
             * 
             *   pre (123);
             */

            elseif ($words[0][0] == '%')
            {
                $code = substr ($line, strlen ($words[0]));

                if (isset ($words[0][1]))
                {
                    if ($words[0][1] == '^')
                    {
                        $parsed = $this->parseSubText ($untouched_lines, $id, $height);

                        $code   .= $parsed[0];
                        $skip_at = $parsed[1];
                    }

                    else throw new \Exception ('Unknown char founded after syntax-control symbol at line "'. $line .'"');
                }
                
                $tree[$id] = [
                    'type'  => VLF_RUNTIME_EXECUTABLE,
                    'line'  => $line,
                    'hard'  => $height,
                    'words' => $words,

                    'info' => [
                        'code' => $code
                    ],

                    'syntax_nodes' => []
                ];
            }

            /**
             * property_name: property_value
             * 
             * ->method_name ([method_arguments])
             * 
             * Form MyForm
             *     Button MyButton
             * 
             */

            elseif (is_int ($current_object) && isset ($tree[$current_object]['hard']))
            {
                if ($height <= $tree[$current_object]['hard'] && isset ($parent_objects[$current_object]))
                {
                    $redirect = $parent_objects[$current_object];

                    $tree[$id] = [
                        'type'  => VLF_OBJECT_REDIRECTION,
                        'line'  => $line,
                        'hard'  => $height,
                        'words' => $words,

                        'info' => [
                            'object_class' => $tree[$redirect]['info']['object_class'],
                            'object_name'  => $tree[$redirect]['info']['object_name']
                        ],

                        'syntax_nodes' => []
                    ];

                    $current_object = $id;
                }

                /**
                 * property_name: property_value
                 */

                $postChar = substr ($words[0], strlen ($words[0]) - 1);

                if ($postChar == ':' || $postChar == '^')
                {
                    if (!isset ($words[1]))
                        throw new \Exception ('Property value mustn\'t be empty at line "'. $line .'"');

                    $propertyName     = substr ($words[0], 0, -1);
                    $propertyValue    = implode (' ', array_slice ($words, 1));
                    $propertyRawValue = ltrim (substr ($line, strlen ($words[0])));

                    /**
                     * property_name:^ property_value_1
                     *                 property_value_2
                     */

                    if ($postChar == '^')
                    {
                        $parsed = $this->parseSubText ($untouched_lines, $id, $height);

                        $skip_at           = $parsed[1];
                        $propertyName      = substr ($propertyName, 0, -1);
                        $propertyRawValue .= $parsed[0];
                        $propertyValue     = $propertyRawValue;
                    }

                    $info = [
                        'type'  => VLF_PROPERTY_SET,
                        'line'  => $line,
                        'hard'  => $height,
                        'words' => $words,

                        'info' => [
                            'property_name'      => $propertyName,
                            'property_value'     => $propertyValue,
                            'property_raw_value' => $propertyRawValue
                        ],

                        'syntax_nodes' => []
                    ];

                    if (isset ($links[$info['info']['property_value']]))
                        $info['info']['property_value'] = new VLFLink ($info['info']['property_value'], $links[$info['info']['property_value']]);

                    $tree[$current_object]['syntax_nodes'][] = $info;
                }

                /**
                 * ->method_name ([method_arguments])
                 */

                elseif (substr ($words[0], 0, 2) == '->')
                {
                    $arguments = [];
                    
                    if (($begin = strpos ($line, '(')) !== false)
                    {
                        ++$begin;
                        
                        $end = strrpos ($line, ')');

                        if ($end === false)
                            throw new \Exception ('Line "'. $line .'" have arguments list initialization, but not have list ending');

                        elseif ($begin < $end)
                        {
                            $parsed = explode (',', substr ($line, $begin, $end - $begin));

                            foreach ($parsed as $argument_id => $argument)
                            {
                                $argument = trim ($argument);

                                if (strlen ($argument) > 0)
                                    $arguments[] = isset ($links[$argument]) ?
                                        new VLFLink ($argument, $links[$argument]) :
                                        $argument;

                                else throw new \Exception ('Argument '. ($argument_id + 1) .' mustn\'t have zero length at line "'. $line .'"');
                            }

                            if (!$this->ignore_postobject_info && trim (substr ($line, $end)) > 0)
                                throw new \Exception ('You mustn\'t write any chars after arguments definition');
                        }
                    }

                    /**
                     * ->show
                     */

                    elseif (!$this->ignore_unexpected_method_args)
                        throw new \Exception ('Unexpected method arguments list at line "'. $line .'"');

                    $tree[$current_object]['syntax_nodes'][] = [
                        'type'  => VLF_METHOD_CALL,
                        'line'  => $line,
                        'hard'  => $height,
                        'words' => $words,

                        'info' => [
                            'method_name'      => substr ($words[0], 2),
                            'method_arguments' => $arguments
                        ],

                        'syntax_nodes' => []
                    ];
                }

                /**
                 * ...я хз что тут должно быть, но первоначально это должно было работать так:
                 * 
                 * Form MainForm
                 *     Button MainButton
                 *         ...
                 * 
                 * И вот весь этот Button и всё, что после него - это и есть VLF_SUBOBJECT_DEFINITION
                 * Но на практике я придумал какой-то дикий костыль в блоке с VLF_OBJECT_DEFINITION
                 * Не вините меня ;D
                 * 
                 * ? UPD: я чекнул АСД главной формы VoidStudio и заметил, что там всё-же где-то да есть эта штука, так что лучше её не трогать и всё оставить как есть ;D
                 * 
                 */

                else
                {
                    $parsed  = $this->parseSubText ($untouched_lines, $id, $height);
                    $skip_at = $parsed[1];
                    
                    $tree[$id] = [
                        'type'  => VLF_SUBOBJECT_DEFINITION,
                        'line'  => $line,
                        'hard'  => $height,
                        'words' => $words,

                        'info' => [
                            'object_vlf_info' => $line ."\n". $parsed[0]
                        ],

                        'syntax_nodes' => []
                    ];
                }
            }

            /**
             * Что-то загадочное, таинственное, неизвестное человечеству
             */

            else throw new \Exception ('Unknown structures founded at line "'. $line .'"');
        }

        return [$tree, $links];
    }

    /**
     * * Парсер подстрок
     * Парсит текст, уровень которого выше, чем указанный
     * 
     * @param array $lines - массив строк
     * @param mixed $begin_id - начальный индекс для парсинга
     * @param int $down_height - нижняя высота, после которой текст парситься не будет
     * 
     * @return array - возвращает спарсенные подстроки
     * 
     */

    protected function parseSubText (array $lines, $begin_id, int $down_height): array
    {
        $parsed = "\n";

        foreach ($lines as $line_id => $line)
        {
            if ($line_id <= $begin_id)
                continue;

            if (!(bool)(trim ($line)))
            {
                $parsed .= "\n";
            
                continue;
            }

            $height = $this->getLineHeight ($line);

            if ($this->debug_mode)
                pre ("$height, $down_height, $line");

            if ($height > $down_height)
                $parsed .= "$line\n";

            else break;
        }

        return [$parsed, $line_id];
    }

    public function __get ($name) // Возвращалка переменных парсера
    {
        return isset ($this->$name) ?
            $this->$name : false;
    }

    /**
     * * Подсчёт высоты строки
     * Производит подсчёт высоты строки и удаляет пустые текстовые символы с обоих её концов
     * 
     * @param string &$line - строка для подсчёта высоты
     * 
     * @return int - высота строки
     * 
     */

    protected function getLineHeight (string &$line): int
    {
        return strlen ($line) - strlen ($line = trim ($line));
    }

    /**
     * * Фильтр строк
     * Удаляет все пустые значения в массиве
     * 
     * @param array $segments - массив строк
     * 
     * @return array - возвращает очищенный массив
     * 
     */

    protected function linesFilter (array $segments): array
    {
        return array_filter ($segments, function ($text)
        {
            if ($this->strong_line_parser && preg_match ('/[^a-z0-9]/i', $text))
                throw new \Exception  ('Line "'. $text .'" mustn\'t have any not-alphabet or not-numeric characters');
            
            return strlen (trim ($text)) > 0;
        });
    }
}

?>
