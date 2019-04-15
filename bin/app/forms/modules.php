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
