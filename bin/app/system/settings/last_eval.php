namespace VoidEngine;

$listBox = new ListBox;
$listBox->items->addRange ([
    123123123,
    'fwefwef',
    'wrwefweiyf'
]);

pre ($listBox->items[1]);

$listBox->items->foreach (function ($index, $value)
{
    pre ($value);
});