namespace VoidEngine;

// $msg = new WFClass ('System.Windows.Forms.MessageBox');
// $msg->show ('text', 'caption', ['YesNo', 'System.Windows.Forms.MessageBoxButtons, System.Windows.Forms']);

$form = new Form;

$item = new MenuItem ('123123123');

$menu = new MainMenu;
$menu->items->addRange ([$item]);

$form->menu = $menu;
$form->show ();