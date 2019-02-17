<?php

namespace VoidEngine;

VLFInterpreter::$throw_errors = false;

const APP_DIR = __DIR__;
chdir (APP_DIR);

foreach (glob ('*.vlf') as $id => $path)
    if (($path = basename ($path)) != 'main.vlf')
        VLFInterpreter::run (new VLFParser (__DIR__ .'/'. $path, [
            'strong_line_parser'            => false,
            'ignore_postobject_info'        => true,
            'ignore_unexpected_method_args' => true,
            
            'use_caching' => '%use_caching%'
        ]), '%resource_dir%');

$APPLICATION->run (VLFInterpreter::run (new VLFParser (__DIR__ .'/main.vlf', [
    'strong_line_parser'            => false,
    'ignore_postobject_info'        => true,
    'ignore_unexpected_method_args' => true,
    
    'use_caching' => '%use_caching%'
]), '%resource_dir%')['%entering_point%']);

?>
