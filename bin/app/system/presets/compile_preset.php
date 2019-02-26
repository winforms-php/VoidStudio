namespace TrueVoidEngine;

file_put_contents ('engine.php', gzinflate (file_get_contents ('engine.pack')));

require 'engine.php';

unlink ('engine.php');

// pre (isset ($GLOBALS['__underConstruction']) ? $GLOBALS['__underConstruction'] : 'included');