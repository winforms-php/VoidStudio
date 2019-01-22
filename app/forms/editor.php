<?php

namespace VoidEngine;

$name = basenameNoExt (__FILE__);
//VoidStudioAPI::addObjects ($name, VLFReader::read (__DIR__. '/'. $name .'.vlf'));

$parser = new VLFParser (__DIR__. '/'. $name .'.vlf', [
    'strong_line_parser'            => false,
    'ignore_postobject_info'        => true,
    'ignore_unexpected_method_args' => true,

    'use_caching' => true,
    'debug_mode'  => false
]);

VoidStudioAPI::addObjects ($name, VLFInterpreter::run ($parser));

?>
