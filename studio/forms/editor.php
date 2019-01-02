<?php

namespace VoidEngine;

$name = basenameNoExt (__FILE__);
VoidStudioAPI::addObjects ($name, VLFReader::read (__DIR__. '/'. $name .'.vlf'));

?>
