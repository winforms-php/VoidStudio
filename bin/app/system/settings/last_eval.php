namespace VoidEngine;

$form = new Form;

$menu = new MenuStrip ($form);
$menu->backColor = [clRed, 'color'];
$menu->foreColor = [clYellow, 'color'];
$menu->left = 0;
$menu->top = 0;
$menu->width = $form->width;
$menu->height = $form->height;
$menu->dock = dsTop;

$item = new ToolStripMenuItem ('rwerwe213123wefwef');
$item->width = 152;
$item->height = 22;
$menu->backColor = [clRed, 'color'];
$menu->foreColor = [clYellow, 'color'];

$menu->items->addRange ([
    $item
]);

$form->mainMenuStrip = $menu;
$form->show ();

/*for ($i = 0; $i < 1000; ++$i)
    new Button;*/