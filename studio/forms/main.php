<?php

namespace VoidEngine;

VoidStudioAPI::addObjects (basenameNoExt (__FILE__), VLFReader::read (__DIR__. '/main.vlf'));

$componentsList = VoidStudioAPI::getObjects ('main')['ComponentsList'];

foreach (array_slice (scandir (ENGINE_DIR .'/components'), 2) as $id => $name)
    if (class_exists ($class = 'VoidEngine\\'. ($name = basenameNoExt ($name))) && array_key_exists ('VoidEngine\Control', class_parents ($class)))
    {
        $item = new ListViewItem ();
        $item->caption = $name;

        $componentsList->items->add ($item);
    }

?>
