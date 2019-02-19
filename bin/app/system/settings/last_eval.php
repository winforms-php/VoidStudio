namespace VoidEngine;

/*$listBox = new ListBox;
$listBox->items->addRange ([
    123123123,
    'fwefwef',
    'wrwefweiyf'
]);

pre ($listBox->items[1]);

$listBox->items->foreach (function ($index, $value)
{
    pre ($value);
});*/

$objs = VoidStudioAPI::getObjects ('main');
VLFImporter::import (APP_DIR .'/forms/about.vlf', $objs['PropertiesList__List'], $objs['EventsList__ActiveEvents'], $objs['PropertiesPanel__SelectedComponent'], $objs['Designer__FormsList']);