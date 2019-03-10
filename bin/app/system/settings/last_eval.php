namespace VoidEngine;

$form = new Form;

$menu = new MenuStrip;
$menu->items->addRange ([
    new ToolStripMenuItem ('rwerwe213123wefwef'),
    new ToolStripMenuItem ('rwerwe213123wefwef'),
    new ToolStripMenuItem ('rwerwe213123wefwef')
]);

$form->mainMenuStrip = $menu;
$form->show ();

/*for ($i = 0; $i < 1000; ++$i)
    new Button;*/