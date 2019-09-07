<?php

namespace VoidEngine;

function packages__updateCaption ()
{
    $source  = VoidStudioAPI::getObjects ('addPackage')['Package__Source'];
    $version = VoidStudioAPI::getObjects ('addPackage')['Package__Version'];

	$source = $source->selectedItem != 'github' ?
        $source->selectedItem .':' : '';
	
	$version = $version->text != 'latest' && $version->text ?
		'@'. $version->text : '';
	
    VoidStudioAPI::getObjects ('addPackage')['Package__Path']->caption = ($package = $source . VoidStudioAPI::getObjects ('addPackage')['Package__Author']->text .'/'. VoidStudioAPI::getObjects ('addPackage')['Package__Name']->text) . $version;

	VoidStudioAPI::getObjects ('addPackage')['Package__Add']->enabled = true;
	
	foreach (VoidStudioAPI::getObjects ('modules')['ModulesList__QeroPackages']->items->list as $line)
		if ((($pos = strrpos ($line, '@')) !== false && substr ($line, 0, $pos) == $package) || ($pos === false && $line == $package))
		{
			VoidStudioAPI::getObjects ('addPackage')['Package__Add']->enabled = false;

			break;
		}
}

function packages__update ()
{
    global $controller;

    $controller->manager->updateManager ();

    $modulesList = VoidStudioAPI::getObjects ('modules')['ModulesList'];
    $modulesList->items->clear ();

    $imageList = new ImageList;
    $qeroGroup = VoidStudioAPI::getObjects ('modules')['ModulesListGroup__QeroPackage'];
    $index = 0;

    foreach ($controller->manager->packages as $package)
    {
        $item = new ListViewItem ('  '. $package->name);
        $item->group      = $qeroGroup;
        $item->imageIndex = $index++;

        $imageList->images->add ((new Image)->loadFromFile (APP_DIR .'/components/icons/Library_16x.png'));
        $modulesList->items->add ($item);
    }
} 

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
