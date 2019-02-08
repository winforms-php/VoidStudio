<?php

namespace VoidEngine;

define ('FRAMEWORK_DIR', getenv ('AppData') .'\VoidFramework');

const CORE_DIR = __DIR__;

if (file_exists ('../core/VoidEngine.php'))
	require '../core/VoidEngine.php';

elseif (file_exists (FRAMEWORK_DIR .'/core/VoidEngine.php'))
	require FRAMEWORK_DIR .'/core/VoidEngine.php';

else message ('VoidEngine not founded');

$app = dirname (CORE_DIR, 2) .'/app/start.php';

if (file_exists ($app))
    require $app;

?>
