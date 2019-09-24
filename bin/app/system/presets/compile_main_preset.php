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

%objects%

foreach (['%forms%'] as $formName)
{
    $class = '\\VoidEngine\\'. $formName;

    $form = new $class;
    $form->name = $formName;

    foreach (get_object_vars ($form) as $name => $value)
        if (is_object ($value) && $value instanceof WFObject)
        {
            // FIXME: это костыль, так делать нельзя

            $value->name = strtoupper ($name[0]) . substr ($name, 1);

            Components::addComponent ($value->selector, $value);
        }

    Components::addComponent ($form->selector, $form);
}

%modules%

$APPLICATION->run (c('%entering_point%'));
