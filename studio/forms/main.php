<?php

namespace VoidEngine;

VoidStudioAPI::addObjects (basenameNoExt (__FILE__), VLFReader::read (__DIR__. '/main.vlf'));

$componentsList = VoidStudioAPI::getObjects ('main')['ComponentsList'];

foreach (array_slice (scandir (ENGINE_DIR .'/components'), 2) as $id => $name)
{
    $item = new ListViewItem ();
    $item->caption = basenameNoExt ($name);

    $componentsList->items->add ($item);
}

?>
