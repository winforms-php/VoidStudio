<?php

namespace VoidBuilder;

use VoidEngine\VoidEngine;

class Builder
{
    public $appDir;

    public function __construct (string $appDir)
    {
        if (!is_dir ($appDir))
            throw new \Exception ('Wrong $appDir param');

        $this->appDir = $appDir;
    }

    public function build (string $outputDir, string $iconPath = null, bool $union = true): array
    {
        \VoidEngine\dir_clean ($outputDir .'/build');
        \VoidEngine\dir_copy (\VoidEngine\CORE_DIR, $outputDir .'/build');

        unlink ($outputDir .'/build/script.php');
        unlink ($outputDir .'/build/VoidCore.exe');

        return VoidEngine::compile ($outputDir .'/build/app.exe', \VoidEngine\text ($iconPath ?? dirname (__DIR__) .'/system/Icon.ico'), \VoidEngine\str_replace_assoc (file_get_contents (dirname (__DIR__) .'/system/preset.php'), [
            '%VoidEngine%' => self::generateCode (self::getReferences (\VoidEngine\ENGINE_DIR .'/VoidEngine.php')),
            '%APP%'        => base64_encode (gzdeflate (serialize ($union ? array_merge (
                self::getFiles ($this->appDir),
                self::getFiles (dirname ($this->appDir) .'/qero-packages', 'qero-packages/KRypt0nn/VoidFramework')
            ) : []), 9))
        ]), null, null, null, null, null, '', '');
    }

    public static function generateCode (array $references, bool $removeNamespaces = true): string
    {
        $code = "/*\n\n\t". join ("\n\t", explode ("\n", file_get_contents (dirname (\VoidEngine\ENGINE_DIR) .'/license.txt'))) ."\n\n*/\n\n";

        foreach ($references as $path)
            $code .= join (array_slice (array_map (function ($line)
            {
                return substr ($line, 0, 7) != 'require' ? $line : '';
            }, file ($path)), 1));

        return $removeNamespaces ?
            preg_replace ('/'. "\n" .'namespace [a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*;'. "\n" .'/', "\n\n", $code) : $code;
    }

    public static function getReferences (string $file, bool $parseExtensions = true): array
    {
        $references = [];

        foreach (file ($file) as $id => $line)
            if (substr ($line, 0, 7) == 'require')
                try
                {
                    $begin = strpos ($line, "'");
                    $end   = strrpos ($line, "'") - $begin + 1;

                    $references = array_merge ($references, self::getReferences (dirname ($file) .'/'. eval ('namespace VoidEngine; return '. substr ($line, $begin, $end) .';'), false));
                }

                catch (\Throwable $e)
                {
                    continue;
                }

        if ($parseExtensions)
            if (is_dir (\VoidEngine\ENGINE_DIR .'/extensions') && is_array ($exts = scandir (\VoidEngine\ENGINE_DIR .'/extensions')))
                foreach ($exts as $id => $ext)
                    if (is_dir (\VoidEngine\ENGINE_DIR .'/extensions/'. $ext) && file_exists ($ext = \VoidEngine\ENGINE_DIR .'/extensions/'. $ext .'/main.php'))
                        $references = array_merge ($references, self::getReferences ($ext, false));

        $references[] = $file;

        return $references;
    }

    public static function getFiles (string $path, string $prefixBlacklist = null, array $files = [], int $originalPathLength = -1): array
    {
        if ($originalPathLength == -1)
            $originalPathLength = strlen (dirname ($path)) + 1;

        $len = strlen ($prefixBlacklist);
        
        foreach (array_slice (scandir ($path), 2) as $name)
            if ($prefixBlacklist === null || substr ($path .'/'. $name, $originalPathLength, $len) != $prefixBlacklist)
            {
                if (is_dir ($file = $path .'/'. $name))
                    $files = self::getFiles ($file, $prefixBlacklist, $files, $originalPathLength);

                else $files[substr ($file, $originalPathLength)] = file_get_contents ($file);
            }

        return $files;
    }
}
