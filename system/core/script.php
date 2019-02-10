<?php

namespace VoidEngine;

define ('FRAMEWORK_DIR', getenv ('AppData') .'\VoidFramework');

const CORE_DIR = __DIR__;
chdir (CORE_DIR);

if (file_exists ('../engine/VoidEngine.php'))
	require '../engine/VoidEngine.php';

elseif (file_exists (FRAMEWORK_DIR .'/engine/VoidEngine.php'))
	require FRAMEWORK_DIR .'/engine/VoidEngine.php';

else message ('VoidEngine not founded');

$app = dirname (CORE_DIR, 2) .'/app/start.php';

if (file_exists ($app))
    require $app;

?>
