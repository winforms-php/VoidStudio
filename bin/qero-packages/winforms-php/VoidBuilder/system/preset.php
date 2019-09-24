namespace VoidEngine;

%VoidEngine%

foreach (unserialize (gzinflate (base64_decode ('%APP%'))) as $path => $content)
    if (!file_exists ($path = str_replace ('..', '', $path)))
    {
        dir_create (dirname ($path));
        
        file_put_contents ($path, $content);
    }

if (file_exists ('qero-packages/autoload.php'))
    file_put_contents ('qero-packages/autoload.php', preg_replace ([
        '%require \'winforms-php/VoidFramework/engine/VoidEngine.php\';%',
        '/array \(\'github:winforms\-php\/VoidFramework\', \'[0-9\.]{1,}\'\)[\,]{0, 1}/'
    ], '// Go your way, Stalker', file_get_contents ('qero-packages/autoload.php')));

if (file_exists ('qero-packages/packages.json'))
{
    $packages = json_decode (file_get_contents ('qero-packages/packages.json'), true);
    unset ($packages['github:winforms-php/VoidFramework']);

    file_put_contents ('qero-packages/packages.json', json_encode ($packages, JSON_PRETTY_PRINT));
}

require 'app/start.php';
