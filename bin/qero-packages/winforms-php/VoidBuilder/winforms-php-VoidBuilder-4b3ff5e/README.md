# VoidBuilder

**VoidBuilder** - консольная **PHP** утилита для сборки приложений, созданных на [**VoidFramework**](https://github.com/KRypt0nn/VoidFramework)

## Установка

1. Скачайте и распакуйте репозиторий
2. Скопируйте [**Qero**](https://github.com/KRypt0nn/Qero) в папку сборщика
3. Установите **Qero**-зависимости
> php Qero.phar install

## Использование

> php build.php [аргументы]

Список доступных аргументов:

| Аргумент | Алиас | Описание |
|-|-|-|
--app-dir | -d | Путь до папки **app**, в которой содержится само **VoidFramework**-приложение. Обязательно
--output-dir | -o | Путь до директории сохранения собранного проекта
--icon-path | -i | Путь до иконки собираемого проекта
--join | -j | Файл или папка, с которой необходимо склеить собранный проект
--no-compress | -nc | При указании отключает распаковку дополнительных файлов при запуске программы *(нежелательно)*

## Примеры

Обычная сборка:
> php build.php -d "C:\Users\\%username%\Desktop\Test App\app" -o C:\Users\\%username%\Desktop

Сборка со склейкой **php7ts.dll**:
> php build.php -d "C:\Users\\%username%\Desktop\Test App\app" -o C:\Users\\%username%\Desktop -j php7ts.dll

Сборка со склейкой **php7ts.dll** и **app**:
> php build.php -d "C:\Users\\%username%\Desktop\Test App\app" -o C:\Users\\%username%\Desktop\t -j php7ts.dll -j "C:\Users\\%username%\Desktop\Test App\app"

Автор: [Подвирный Никита](https://vk.com/technomindlp). Специально для [Enfesto Studio Group](https://vk.com/hphp_convertation)