<?php

namespace VoidBuilder;

use ConsoleArgs\{
    Manager,
    DefaultCommand,
    Param,
    Flag
};

require 'VoidBuilder.php';

if (!isset ($argv))
{
	$params = json_decode (file_get_contents (__DIR__ .'/params.json'), true);
	$oargv = [__FILE__];
	
	unlink (__DIR__ .'/params.json');
	
    foreach ($params as $name => $param)
    {
        if (!is_array ($param))
            $param = [$param];
        
        foreach ($param as $arg)
            if (strlen ($arg) > 0)
            {
                $oargv[] = $name;
                $oargv[] = $arg;
            }
    }
    
    if (!defined ('VoidBuilder\ENGINE_DIR'))
        define ('VoidBuilder\ENGINE_DIR', $params['--engine-dir']);

    if (!defined ('VoidBuilder\CORE_DIR'))
        define ('VoidBuilder\CORE_DIR', dirname (ENGINE_DIR) .'/core');
    
	define ('VoidEngine\CORE_DIR', dirname (ENGINE_DIR) .'/core');
    
    require $params['--engine-dir'] .'/VoidEngine.php';
	
	$argv = $oargv;
}

try
{
    (new Manager ([], (new DefaultCommand (function ($args, $params)
    {
        foreach (['--app-dir', '--output-dir', '--icon-path'] as $param)
            if (is_array ($params[$param]))
                $params[$param] = end ($params[$param]);

        if (!file_exists (dirname ($params['--app-dir']) .'/qero-packages/winforms-php/VoidFramework/core/VoidCore.exe'))
            die ("\n Incorrect VoidFramework app path\n");

        if (class_exists ('VoidEngine\WFObject'))
        {
            $errors = (new Builder ($params['--app-dir']))
                ->build ($params['--output-dir'], $params['--icon-path'], !$params['--no-compress']);

            if (sizeof ($errors) > 0)
                print_r ($errors);
        }

        else
        {
            echo PHP_EOL;
            echo ' Building ['. dirname (str_replace (dirname ($params['--app-dir'], 2) .'\\', '', $params['--app-dir'])) .']...'. PHP_EOL . PHP_EOL;

            $begin = microtime (true);

            $params['--engine-dir'] = dirname ($params['--app-dir']) .'/qero-packages/winforms-php/VoidFramework/engine';
            file_put_contents ('params.json', json_encode ($params, JSON_PRETTY_PRINT));

            shell_exec ('"'. dirname ($params['--app-dir']) .'/qero-packages/winforms-php/VoidFramework/core/VoidCore.exe" "'. __FILE__ .'"');

            echo ' Building completed after '. round (microtime (true) - $begin, 6) .' seconds'. PHP_EOL;
            echo '   Saved at '. $params['--output-dir'] .'/build'. PHP_EOL . PHP_EOL;

            if (isset ($params['--join']))
            {
                if (!is_array ($params['--join']))
                    $params['--join'] = [$params['--join']];

                if (($size = sizeof ($params['--join'])) > 0)
                {
                    echo ' Union '. $size .' files...'. PHP_EOL;

                    $begin  = microtime (true);
                    $joiner = new Joiner ($params['--output-dir'] .'/build/app.exe', $params['--output-dir'] .'/app.exe');
                    
                    foreach ($params['--join'] as $file)
                        $joiner->add (file_exists ($file) ? $file : $params['--output-dir'] .'/build/'. $file);

                    echo str_replace ("\n", "\n ", $joiner->join ()) . PHP_EOL;
                    echo ' Union completed after '. round (microtime (true) - $begin, 6) .' seconds'. PHP_EOL;
                    echo '   Saved at '. $params['--output-dir'] . PHP_EOL;
                }
            }
        }
    }))->addParams ([
        (new Param ('--app-dir', null, true))->addAliase ('-d'),
        (new Param ('--output-dir', __DIR__ .'/build'))->addAliase ('-o'),
        (new Param ('--icon-path', __DIR__ .'/system/Icon.ico'))->addAliase ('-i'),
        (new Param ('--join'))->addAliase ('-j'),
        (new Flag ('--no-compress'))->addAliase ('-nc')
    ])))->execute ($argv);
}

catch (\Exception $e)
{
    die ("\n ". $e->getMessage () ."\n");
}
