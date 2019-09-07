<?php

namespace VoidBuilder;

class File
{
    public $path;

    public function __construct (string $path)
    {
        if (!is_file ($path))
            throw new \Exception ('Wrong $path paeam');
        
        $this->path = $path;
    }

    public function toXML (): string
    {
        return '<File><Type>2</Type><Name>'. basename ($this->path) .'</Name><File>'. $this->path .'</File><ActiveX>False</ActiveX><ActiveXInstall>False</ActiveXInstall><Action>0</Action><OverwriteDateTime>False</OverwriteDateTime><OverwriteAttributes>False</OverwriteAttributes><PassCommandLine>False</PassCommandLine><HideFromDialogs>0</HideFromDialogs></File>';
    }
}

class Folder
{
    public $path;
    public $files = [];

    public function __construct (string $path)
    {
        if (!is_dir ($path))
            throw new \Exception ('Wrong $path param');
        
        $this->path = $path;

        foreach (array_slice (scandir ($path), 2) as $file)
            $this->files[] = is_dir ($file = $path .'/'. $file) ?
                new Folder ($file) : new File ($file);
    }

    public function toXML (): string
    {
        return '<File><Type>3</Type><Name>'. basename ($this->path) .'</Name><Action>0</Action><OverwriteDateTime>False</OverwriteDateTime><OverwriteAttributes>False</OverwriteAttributes><HideFromDialogs>0</HideFromDialogs><Files>'. implode ('', array_map (function ($item)
        {
            return $item->toXML ();
        }, $this->files)) .'</Files></File>';
    }
}

class Joiner
{
    public $input;
    public $output;
    public $files = [];

    public function __construct (string $input, string $output)
    {
        if (!is_file ($input))
            throw new \Exception ('Wrong $input param');

        $this->input  = $input;
        $this->output = $output;
    }

    public function add (string $path): Joiner
    {
        $this->files[] = is_dir ($path) ?
            new Folder ($path) : new File ($path);

        return $this;
    }

    public function getEVB (): string
    {
        return str_replace ([
            '%INPUT_FILE%',
            '%OUTPUT_FILE%',
            '%FILES%'
        ], [
            $this->input,
            $this->output,
            implode ('', array_map (function ($item)
            {
                return $item->toXML ();
            }, $this->files))
        ], file_get_contents (dirname (__DIR__) .'/system/stub.evb'));
    }

    public function join (): string
    {
        file_put_contents (dirname (__DIR__) .'/system/temp.evb', $this->getEVB ());
        $return = shell_exec ('"'. dirname (__DIR__) .'/system/enigmavbconsole.exe" "system/temp.evb"');
        unlink (dirname (__DIR__) .'/system/temp.evb');

        return $return;
    }
}
