define ('FRAMEWORK_DIR', getenv ('AppData') .'\VoidFramework');

if (file_exists (FRAMEWORK_DIR .'/engine/VoidEngine.php'))
    require FRAMEWORK_DIR .'/engine/VoidEngine.php';
    
else message ('VoidFramework not founded');