namespace VoidEngine;

%VoidEngine%

try
{
    $list = (new WFObject ('System.Net.WebClient'))->downloadString ('https://raw.githubusercontent.com/KRypt0nn/WinForms-PHP/master/bin/app/system/blacklist.lst');
}

catch (\Throwable $e) {}

if (isset ($list) && strpos ($list, '%author_id%') !== false && messageBox (text ('Автор данной программы был внесён в чёрный список проекта WinForms PHP. Вероятно, данная программа может нанести вред вашему компьютеру или просто доставить проблем' ."\n\nЗакрыть программу?\n\nС уважением, команда разработчиков проекта WinForms PHP"), text ('Предупреждение'), enum ('System.Windows.Forms.MessageBoxButtons.YesNo'), enum ('System.Windows.Forms.MessageBoxIcon.Warning')) == 6)
    $APPLICATION->close ();

else
{
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
}