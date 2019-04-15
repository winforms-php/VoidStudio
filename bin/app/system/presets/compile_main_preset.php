namespace VoidEngine;

try
{
    $client = winforms_createObject ('System.Net.WebClient', 'System.Net');
    $list = winforms_callMethod ($client, 'DownloadString', 'https://raw.githubusercontent.com/KRypt0nn/WinForms-PHP/master/bin/app/system/blacklist.lst');

    winforms_removeObjects ($client);
    
    $client = winforms_createObject ('System.Windows.Forms.MessageBox', 'System.Windows.Forms');
}

catch (\Throwable $e) {}

if (isset ($list) && strpos ($list, '%author_id%') !== false && winforms_callMethod ($client, 'Show', winforms_to1251 ('Автор данной программы был внесён в чёрный список проекта WinForms PHP. Вероятно, данная программа может нанести вред вашему компьютеру или просто доставить проблем' ."\n\nЗакрыть программу?\n\nС уважением, команда разработчиков проекта WinForms PHP"), winforms_to1251 ('Предупреждение'),  ['System.Windows.Forms.MessageBoxButtons.YesNo', 'System.Windows.Forms.MessageBoxButtons'], ['System.Windows.Forms.MessageBoxIcon.Warning', 'System.Windows.Forms.MessageBoxIcon']) == 6)
    winforms_callMethod (winforms_createObject ('System.Windows.Forms.Application', 'System.Windows.Forms'), 'Close');

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

    $enteringPoint = $GLOBALS['__underConstruction']['%entering_point%']['%entering_point%'];
    unset ($GLOBALS['__underConstruction']);

    $APPLICATION->run ($enteringPoint);
}

else throw new \Exception ('Objects not initialized');
