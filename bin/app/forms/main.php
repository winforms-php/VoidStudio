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
$imageList      = new ImageList;
$components     = json_decode (file_get_contents ('components/components.json'), true);
$index          = 0;

foreach ($components as $groupName => $comps)
{
    $group = new ListViewGroup (text ($groupName));

    $componentsList->groups->add ($group);

    foreach ($comps as $component)
    {
        $item = new ListViewItem ('  '. $component);
        $item->group      = $group;
        $item->imageIndex = $index++;

        $path = text (APP_DIR .'/components/icons/'. $component .'_16x.png');

        if (!file_exists ($path))
            $path = text (APP_DIR .'/components/icons/Unknown_16x.png');

        $imageList->images->add ((new Image)->loadFromFile ($path));
        $componentsList->items->add ($item);
    }
}

$componentsList->smallImageList = $imageList;

?>
