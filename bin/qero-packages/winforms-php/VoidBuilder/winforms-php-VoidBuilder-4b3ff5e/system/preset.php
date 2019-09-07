namespace VoidEngine;

%VoidEngine%

foreach (unserialize (gzinflate (base64_decode ('%APP%'))) as $path => $content)
    if (!file_exists ($path = str_replace ('..', '', $path)))
    {
        dir_create (dirname ($path));
        
        file_put_contents ($path, $content);
    }

if (file_exists ('qero-packages/autoload.php'))
    file_put_contents ('qero-packages/autoload.php', preg_replace ('%require \'KRypt0nn/VoidFramework/KRypt0nn-VoidFramework-[a-f0-9]{7}/engine/VoidEngine.php\';%', '// Go your way, Stalker', file_get_contents ('qero-packages/autoload.php')));

if (file_exists ('qero-packages/packages.json'))
{
    $packages = json_decode (file_get_contents ('qero-packages/packages.json'), true);
    unset ($packages['github:KRypt0nn/VoidFramework']);

    file_put_contents ('qero-packages/packages.json', json_encode ($packages, JSON_PRETTY_PRINT));
}

require 'app/start.php';
