# ConsoleArgs

Класс для реализации работы с аргументами командной строки для **PHP** 7+

Примеры работы *(пусть наш файл будет называться index.php)*:

```php
<?php

namespace ConsoleArgs;

(new Manager ([
    new Command ('hello', function ()
    {
        echo 'Hello, World!';
    })
]))->execute (array_slice ($argv, 1));

// array_slice нужен чтобы отрезать аргумент вызова файла из консоли
```

---

```cmd
php index.php hello
```

Вывод:
```
Hello, World!
```

### Работа с аргументами

Для получения списка аргументов, переданных команде, вы можете добавить параметр анонимной функции команде:

```php
<?php

namespace ConsoleArgs;

(new Manager ([
    new Command ('write', function ($args)
    {
        echo implode (' ', $args);
    })
]))->execute (array_slice ($argv, 1));
```

---

```cmd
php index.php write kek lol arbidol
```

Вывод:
```
kek lol arbidol
```

### Работа с параметрами

Функционал параметров предоставляет объект **Param**, флагов - объект **Flag**:

```php
<?php

namespace ConsoleArgs;

(new Manager ([
    (new Command ('write', function ($args, $params)
    {
        // Если было указано несколько одинаковых параметров, то будет указан список всех введённых значений
        // Поэтому это так же нужно предусмотреть:
        if (is_array ($params['--glue']))
            $params['--glue'] = $params['--glue'][0];
        
        echo $params['--base64'] ?
            base64_encode (implode ($params['--glue'], $args)) :
            implode ($params['--glue'], $args);
    }))->addParams ([
        // Первый аргумент - название параметра
        // Второй аргумент (необязательный) - значение по умолчанию
        // Третий аргумент (необязательный) - обязательно ли нужно использовать данный параметр
        new Param ('--glue', ' ', true),

        // Аргумент - название флага
        // "-b64" - алиас флага (альтернативное название)
        (new Flag ('--base64'))->addAliase ('-b64')
    ])
], new DefaultCommand (function ()
{
    echo 'You should write correct command name';
})))->execute (array_slice ($argv, 1));
```

---

```cmd
php index.php write kek lol arbidol
```

Вывод:
```
(исключение, т.к. не был использован параметр --glue)
```

---

```cmd
php index.php write kek lol arbidol --glue ", "
```

Вывод:
```
kek, lol, arbidol
```

---

```cmd
php index.php write kek lol arbidol --glue ", " --base64
```

Вывод:
```
a2VrLCBsb2wsIGFyYmlkb2w=
```

### Разветвление команд

В анонимной функции команды вы можете создать новый менеджер команд с новыми командами. Тем самым вы можете сделать команды для... команды... да...

```php
<?php

namespace ConsoleArgs;

(new Manager ([
    new Command ('test', function ($args)
    {
        (new Manager ([
            new Command ('1', function ()
            {
                echo 'Enfesto Studio'. PHP_EOL;
            }),

            new Command ('2', function ()
            {
                echo 'WinForms PHP'. PHP_EOL;
            }),

            new Command ('3', function ()
            {
                echo 'Every Software'. PHP_EOL;
            })
        ]))->execute ($args);
    })
]))->execute (array_slice ($argv, 1));
```

---

```cmd
php index.php test 1
```

Вывод:
```
Enfesto Studio
```

### Локализации

За локализации отвечает объект **Locale**. Подробнее - см. содержимое класса

Автор: [Подвирный Никита](https://vk.com/technomindlp). Специально для [Enfesto Studio Group](https://vk.com/hphp_convertation)
