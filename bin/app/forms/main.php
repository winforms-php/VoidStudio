<?php

namespace VoidEngine;

$name = basenameNoExt (__FILE__);

$parser = new VLFParser (__DIR__. '/'. $name .'.vlf', [
    'strong_line_parser'            => false,
    'ignore_postobject_info'        => true,
    'ignore_unexpected_method_args' => true,

    'use_caching' => true,
    'debug_mode'  => false
]);

// file_put_contents ('SyntaxTree__'. $name .'.json', json_encode ($parser->tree, JSON_PRETTY_PRINT));

VoidStudioAPI::addObjects ($name, VLFInterpreter::run ($parser));

$componentsList = VoidStudioAPI::getObjects ('main')['ComponentsList'];

foreach (scandir (ENGINE_DIR .'/components') as $id => $name)
    if (class_exists ($class = 'VoidEngine\\'. ($name = basenameNoExt ($name))) && array_key_exists ('VoidEngine\Component', class_parents ($class)) && !array_key_exists ('VoidEngine\NoVisual', class_parents ($class)))
    {
        $item = new ListViewItem ($name);

        $componentsList->items->add ($item);
        // $componentsList->smallImagesList->images->add ((new Bitmap (APP_DIR .'/components/Button_16x.png'))->selector);
    }

?>
