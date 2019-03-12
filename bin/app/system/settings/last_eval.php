namespace VoidEngine;

$form = new Form;

$menu = new MenuStrip;
$menu->stretch = true;

$item = new ToolStripMenuItem ('rwerwe213123wefwef');
$item->size = [80, 20];

$menu->items->addRange ([
    $item
]);

$menu->dock = dsTop;
$form->mainMenuStrip = $menu;
$form->show ();

/*for ($i = 0; $i < 1000; ++$i)
    new Button;*/