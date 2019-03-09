namespace VoidEngine;

$form = new Form;

$button = new Button ($form);
$button->image = (new Image ())->loadFromFile (text (APP_DIR .'/system/icons/Run_16x.png'));

$form->show ();

/*for ($i = 0; $i < 1000; ++$i)
    new Button;*/