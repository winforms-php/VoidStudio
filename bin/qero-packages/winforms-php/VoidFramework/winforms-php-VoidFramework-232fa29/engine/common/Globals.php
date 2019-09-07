<?php

namespace VoidEngine;

$APPLICATION = new class
{
    public $application;
    public $executablePath;
    
    public function __construct ()
    {
        $this->application    = new WFClass ('System.Windows.Forms.Application');
        $this->executablePath = $this->application->executablePath;
    }
    
    public function run ($form = null): void
    {
        if ($form instanceof WFObject)
            $this->application->run ($form->selector);
        
        elseif (is_int ($form) && VoidEngine::objectExists ($form))
            $this->application->run ($form);
        
        elseif ($form === null)
            $this->application->run ();

        else throw new \Exception ('$form param must be instance of "VoidEngine\WFObject" ("VoidEngine\Form"), be null or object selector');
    }
    
    public function restart (): void
    {
        $this->application->restart ();
        $this->close ();
    }
    
    public function close (): void
    {
        $this->application->exit ();
    }

    public function __call (string $name, array $args)
    {
        return $this->application->$name (...$args);
    }

    public function __get (string $name)
    {
        return $this->application->$name;
    }
};

$SCREEN = new class
{
    public $screen;
    
    public function __construct ()
    {
        $this->screen = new WFClass ('System.Windows.Forms.Screen');
    }
    
    public function __get ($name)
    {
        switch (strtolower ($name))
        {
            case 'width':
            case 'w':
                return $this->screen->primaryScreen->bounds->width;
            break;
            
            case 'height':
            case 'h':
                return $this->screen->primaryScreen->bounds->height;
            break;

            default:
                return $this->screen->$name;
            break;
        }
    }
    
    public function __debugInfo (): array
    {
        return [
            $this->w,
            $this->h
        ];
    }
};
