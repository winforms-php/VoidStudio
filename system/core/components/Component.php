<?php

namespace VoidEngine;

abstract class Component
{
    protected $componentSelector;
    protected $componentClass;
    public $helpStorage = '';
	
	public function __construct (string $className)
	{
        $className = substr ($className, ($pos = strrpos ($className, '\\')) !== false ? $pos + 1 : 0);

        $this->componentSelector = VoidEngine::createObject (new WFObject ("System.Windows.Forms.$className"));
        $this->componentClass    = $className;
        
        Components::addComponent ($this->componentSelector, $this);
	}
	
	public function __get ($name)
	{
        if (method_exists ($this, $method = "get_$name"))
            return $this->$method ();
            
        elseif (substr ($name, strlen ($name) - 5) == 'Event')
            return Events::getObjectEvent ($this, substr ($name, 0, -5));

        elseif (property_exists ($this, $name))
            return $this->$name;
        
        else return $this->getProperty ($name);
	}
	
	public function __set ($name, $value)
	{
        if (method_exists ($this, $method = "set_$name"))
            return $this->$method ($value);
            
        elseif (substr ($name, strlen ($name) - 5) == 'Event')
            Events::setObjectEvent ($this, substr ($name, 0, -5), $value);
        
        else $this->setProperty ($name, $value);
	}
	
	public function __call ($method, $args)
	{
        $this->callMethod ($method, ...$args);
	}
	
    protected function getProperty ($name)
    {
        return VoidEngine::getProperty ($this->componentSelector, $name);
    }
	
	protected function getArrayProperty ($name, string $type = null)
	{
        $array  = $this->getProperty ($name);
        $size   = VoidEngine::getProperty ($array, 'Length');
        $return = [];

		for ($i = 0; $i < $size; ++$i)
            $return[] = VoidEngine::getArrayValue ($array, $type === null ? $i : [$i, $type]);
        
        VoidEngine::removeObject ($array); // Maybe это привидёт к тому, что нельзя будет обратиться к массиву $name несколько раз. Ну, посмотрим, так сказать)
        
		return $return;
	}
	
    protected function setProperty (string $name, $value)
    {
        VoidEngine::setProperty ($this->componentSelector, $name, $value);
    }
	
    protected function callMethod ($method, ...$args) 
    { 
        return VoidEngine::callMethod ($this->componentSelector, $method, ...$args); 
    }
	
    public function get_selector ()
    {
        return $this->componentSelector;
    }
	
    function __toString ()
    {
        return $this->callMethod ('ToString');
    }
	
    public function __debugInfo ()
    {
        return [
            'description' => $this->callMethod ('ToString'),
            'selector'    => $this->componentSelector
        ];
    }
	
	public function dispose ()
	{
        if (is_int ($this->componentSelector))
        {
            $this->callMethod ('Dispose');

            Components::removeComponent ($this->componentSelector);
            VoidEngine::removeObject ($this->componentSelector);
            
            unset ($this->componentSelector, $this->componentClass, $this->helpStorage);
        }

        else throw new \Exception ('Object already disposed');
    }
}

?>
