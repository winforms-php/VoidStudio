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
	
	final function __get ($name)
	{
        if (method_exists ($this, $method = "get_$name"))
            return $this->$method ();
            
        elseif (substr ($name, strlen ($name) - 5) == 'Event')
            return Events::getObjectEvent ($this, substr ($name, 0, -5));

        elseif (property_exists ($this, $name))
            return $this->$name;
        
        else return $this->getProperty ($name, '');
	}
	
	final function __set ($name, $value)
	{
        if (method_exists ($this, $method = "set_$name"))
            return $this->$method ($value);
            
        elseif (substr ($name, strlen ($name) - 5) == 'Event')
            Events::setObjectEvent ($this, substr ($name, 0, -5), $value);
		
		elseif (method_exists ($this, "get_$name"))
			throw new \Exception ("The \"$name\" property of the \"$this->componentClass\" component is read-only");
        
        else $this->setProperty ($name, $value, 'auto');
	}
	
	final function __call ($method, $args)
	{
        if (strtoupper ($method[0]) == $method[0])
            $this->callMethod ($method, ...$args);

        else throw new \Exception ("The \"$method\" method is missing from the \"$this->componentClass\" component");
	}
	
    final protected function getProperty (string $name, string $type)
    {
        return VoidEngine::getProperty ($this->componentSelector, $name, $type);
    }
	
	final protected function getArrayProperty (string $name, string $type)
	{
        $array  = $this->getProperty ($name, 'object');
        $size   = VoidEngine::getProperty ($array, 'Length', 'int');
        $return = [];

		for ($i = 0; $i < $size; ++$i)
            $return[] = VoidEngine::getArrayValue ($array, $i, $type);
        
        VoidEngine::removeObject ($array); // May be это привидёт к тому, что нельзя будет обратиться к массиву $name несколько раз. Ну, посмотрим, так сказать)
        
		return $return;
	}
	
    final protected function setProperty (string $name, $value, string $type)
    {
        VoidEngine::setProperty ($this->componentSelector, $name, $value, $type);
    }
	
    final protected function callMethod (string $method, ...$args) 
    { 
        return VoidEngine::callMethod ($this->componentSelector, $method, ...$args); 
    }
	
    final public function get_selector ()
    {
        return $this->componentSelector;
    }
	
    final function __toString ()
    {
        return $this->callMethod ('ToString', 'string');
    }
	
    public function __debugInfo ()
    {
        return [
            'description' => $this->callMethod ('ToString', 'string'),
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
