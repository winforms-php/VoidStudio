<?php

namespace VoidEngine;

class VoidStudioAPI
{
    static $objects      = [];
    static $projectPath  = WORKSPACE;
    static $closeApp     = true;
    static $oldFilesHash = [];

    static function openProject (string $path)
    {
        self::$projectPath = $path;
        self::$closeApp    = false;

        setTimer (1000, function () use ($path)
        {
            VoidStudioAPI::buildExplorerTree ($path, VoidStudioAPI::getObjects ('main')['Project']);

            $edit = VoidStudioAPI::getObjects ('main')['Editor'];
            $path = substr ($edit->helpStorage, strpos ($edit->helpStorage, '\\'));
            
            if (!is_file (VoidStudioAPI::$projectPath .'/'. $path))
            {
                $edit->helpStorage = '';
                $edit->text        = '';

                VoidStudioAPI::getObjects ('main')['Caption']->caption = 'VoidStudio';
            }
        });

        self::getObjects ('load')['MainForm']->dispose ();
    }

    static function buildExplorerTree (string $path, $parentNode, bool $forceUpdate = false)
    {
        $files = array_diff (scandir ($path), ['.', '..']);
        $hash  = self::getDirectoryHash ($path, $files);
        $break = false;

        if (isset (self::$oldFilesHash[$path]) && $hash == self::$oldFilesHash[$path])
            $break = true;

        self::$oldFilesHash[$path] = $hash;

        if ($break)
            return;

        $parentNode->nodes->clear ();

        foreach ($files as $id => $file)
        {
            $node = new TreeNode;
            $node->caption = $file;

            $parentNode->nodes->add ($node);

            if (is_dir ("$path/$file"))
                self::buildExplorerTree ("$path/$file", $node);
        }
    }

    static function getDirectoryHash (string $path, array $files = null, string $hashLine = '')
    {
        if ($files === null)
            $files = array_diff (scandir ($path), ['.', '..']);

        foreach ($files as $id => $file)
            $hashLine .= is_file ("$path/$file") ?
            "$path/$file" : self::getDirectoryHash ("$path/$file", null, $hashLine);

        return sha1 ($hashLine);
    }

    static function addObjects (string $group, array $objects)
    {
        self::$objects[$group] = array_merge (
            isset (self::$objects[$group]) ? self::$objects[$group] : [],
            $objects
        );
    }

    static function getObjects (string $group)
    {
        return isset (self::$objects[$group]) ?
            self::$objects[$group] : false;
    }
}

?>
