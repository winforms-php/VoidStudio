<?php

/*
    Класс, отвечающий за объявление суперглобальных переменных-объектов
*/

namespace VoidEngine;

$APPLICATION = new class
{
    public $executablePath;
    public $application;
    
    public function __construct ()
    {
        $this->application    = new WFClass ('System.Windows.Forms.Application');
        $this->executablePath = $this->application->executablePath;
    }
    
    public function run (Form $form = null): void
    {
        $form !== null ?
            $this->application->run ($form->selector) :
            $this->application->run ();
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
                $screen = $this->screen->primaryScreen;
                $bounds = VoidEngine::getProperty ($screen, 'Bounds');
                $width  = VoidEngine::getProperty ($bounds, 'Width');

                voidEngine::removeObject ($screen, $bounds);

                return $width;
            break;
            
            case 'height':
            case 'h':
                $screen = $this->screen->primaryScreen;
                $bounds = VoidEngine::getProperty ($screen, 'Bounds');
                $height = VoidEngine::getProperty ($bounds, 'Height');

                voidEngine::removeObject ($screen, $bounds);

                return $height;
            break;

            default:
                return $this->screen->$name;
            break;
        }
    }
    
    public function __debugInfo (): array
    {
        return [$this->w, $this->h];
    }
};

?>
