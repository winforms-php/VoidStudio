<?php

/*
    Класс, отвечающий за объявление суперглобальных переменных-объектов
*/

namespace VoidEngine;

$APPLICATION = new class
{
    public $executablePath;
    protected $selector;
    
    public function __construct ()
    {
        $this->selector       = VoidEngine::buildObject (new WFObject ('System.Windows.Forms.Application'));
        $this->executablePath = VoidEngine::getProperty ($this->selector, 'ExecutablePath', 'string');
    }
    
    public function run (Form $form = null): void
    {
        $form ?
            VoidEngine::callMethod ($this->selector, 'Run', '', $form->selector, 'object') :
            VoidEngine::callMethod ($this->selector, 'Run');
    }
    
    public function restart (): void
    {
        VoidEngine::callMethod ($this->selector, 'Restart');
    }
    
    public function close (): void
    {
        VoidEngine::callMethod ($this->selector, 'Exit');
    }
};

$SCREEN = new class
{
    protected $selector;
    
    public function __construct ()
    {
        $this->selector = VoidEngine::buildObject (new WFObject ('System.Windows.Forms.Screen'));
    }
    
    public function __get ($name)
    {
        switch (strtolower ($name))
        {
            case 'width':
            case 'w':
                $screen = VoidEngine::getProperty ($this->selector, 'PrimaryScreen', 'object');
                $bounds = VoidEngine::getProperty ($screen, 'Bounds', 'object');
                $width  = VoidEngine::getProperty ($bounds, 'Width', 'int');

                voidEngine::removeObject ($screen, $bounds);

                return $width;
            break;
            
            case 'height':
            case 'h':
                $screen = VoidEngine::getProperty ($this->selector, 'PrimaryScreen', 'object');
                $bounds = VoidEngine::getProperty ($screen, 'Bounds', 'object');
                $height = VoidEngine::getProperty ($bounds, 'Height', 'int');

                voidEngine::removeObject ($screen, $bounds);

                return $height;
            break;

            default:
                if (strtoupper ($name[0]) == $name[0])
                    return VoidEngine::getProperty ($this->selector, $name, '');

                else throw new \Exception ('Wrong $SCREEN property name');
            break;
        }
    }
    
    public function __debugInfo (): array
    {
        return [$this->w, $this->h];
    }
};

?>
