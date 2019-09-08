namespace VoidEngine;

try
{
    $client = \VoidCore::createObject ('System.Net.WebClient', 'System.Net');
    $list = \VoidCore::callMethod ($client, 'DownloadString', 'https://raw.githubusercontent.com/winforms-php/VoidStudio/master/bin/app/system/blacklist.lst');

    \VoidCore::removeObjects ($client);
    
    $client = \VoidCore::createObject ('System.Windows.Forms.MessageBox', 'System.Windows.Forms');
}

catch (\Throwable $e) {}

if (isset ($list) && strpos ($list, '%author_id%') !== false && \VoidCore::callMethod ($client, 'Show', 'Автор данной программы был внесён в чёрный список проекта WinForms PHP. Вероятно, данная программа может нанести вред вашему компьютеру или просто доставить проблем' ."\n\nЗакрыть программу?\n\nС уважением, команда разработчиков проекта WinForms PHP", 'Предупреждение',  ['System.Windows.Forms.MessageBoxButtons.YesNo', 'System.Windows.Forms.MessageBoxButtons'], ['System.Windows.Forms.MessageBoxIcon.Warning', 'System.Windows.Forms.MessageBoxIcon']) == 6)
    \VoidCore::callMethod (\VoidCore::createObject ('System.Windows.Forms.Application', 'System.Windows.Forms'), 'Close');

foreach (glob (__DIR__ .'/ext/php_*.dll') as $ext)
	if (!extension_loaded (substr (basename ($ext), 4, -4)))
		load_extension ($ext);

%VoidEngine%

%modules%

if (isset ($GLOBALS['__underConstruction']))
{
    foreach ($GLOBALS['__underConstruction'] as $group => $objects)
        foreach ($objects as $name => $selector)
        {
            $object = new Control (null, $selector);

            try
            {
                $object->name = $name;
            }

            catch (\Throwable $e) {}

            Components::addComponent ($selector, $object);
        }

%events%

    $enteringPoint = $GLOBALS['__underConstruction']['%entering_point%']['%entering_point%'];
    unset ($GLOBALS['__underConstruction']);

    $APPLICATION->run ($enteringPoint);
}

else throw new \Exception ('Objects not initialized');
