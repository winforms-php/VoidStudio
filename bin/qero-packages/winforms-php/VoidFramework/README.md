<h1 align="center">VoidFramework</h1>

<p align="center">
    <a href="https://scrutinizer-ci.com/g/winforms-php/VoidFramework/?branch=master"><img src="https://scrutinizer-ci.com/g/winforms-php/VoidFramework/badges/quality-score.png?b=master"></a>
    <a href="https://scrutinizer-ci.com/g/winforms-php/VoidFramework/build-status/master"><img src="https://scrutinizer-ci.com/g/winforms-php/VoidFramework/badges/build.png?b=master"></a>
    <a href="https://scrutinizer-ci.com/code-intelligence"><img src="https://scrutinizer-ci.com/g/winforms-php/VoidFramework/badges/code-intelligence.svg?b=master"></a>
    <a href="license.txt"><img src="https://badges.frapsoft.com/os/gpl/gpl.png?v=103"></a>
</p>

<p align="center"><b>VoidFramework</b> - инструмент для создания графических приложений для <b>Windows</b> на базе <b>.NET Framework</b> и <b>PHP</b></p><br>

## Установка (Qero)

```cmd
php Qero.phar install winforms-php/VoidFramework
```

> Qero: [тык](https://github.com/KRypt0nn/Qero)

## Использование

После установки создастся папка **app** рядом с папкой **qero-packages**. В ней размещается само приложение **VoidFramework**. В качестве точки входа используется файл **start.php**

Для запуска приложения вы можете прописать команду

```cmd
php Qero.phar start
```

запустить файл **start.bat** или создать какой-нибудь ярлык. Это не суть важно. Запуск приложения происходит через файл **%VoidFramework%/core/VoidCore.exe** с аргументом в виде пути к файлу точки входа *(подробнее в **start.bat**)*

Подробности функционала **VoidEngine** смотреть в основном проекте *(ссылка выше)*

Авторы: [Подвирный Никита](https://vk.com/technomindlp) и [Андрей Кусов](https://vk.com/postmessagea)
