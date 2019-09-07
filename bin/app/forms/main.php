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

$controlGroup = new ListViewGroup ('Управление');
$componentsList->groups->add ($controlGroup);

foreach ($components as $groupName => $comps)
{
    if ($groupName[0] != '-')
    {
        $group = new ListViewGroup ($groupName);
        $componentsList->groups->add ($group);

        foreach ($comps as $component)
        {
            $item = new ListViewItem ('  '. $component);
            $item->group      = $group;
            $item->imageIndex = $index++;

            $path = APP_DIR .'/components/icons/'. $component .'_16x.png';

            if (!file_exists ($path))
                $path = APP_DIR .'/components/icons/Unknown_16x.png';

            $imageList->images->add ((new Image)->loadFromFile ($path));
            $componentsList->items->add ($item);
        }
    }

    else
    {
        $item = new ListViewItem ('  '. $comps);
        $item->group      = $controlGroup;
        $item->imageIndex = $index++;

        $path = APP_DIR .'/components/icons/'. substr ($groupName, 1) .'_16x.png';

        if (!file_exists ($path))
            $path = APP_DIR .'/components/icons/Unknown_16x.png';

        $imageList->images->add ((new Image)->loadFromFile ($path));
        $componentsList->items->add ($item);
    }
}

$componentsList->smallImageList = $imageList;
