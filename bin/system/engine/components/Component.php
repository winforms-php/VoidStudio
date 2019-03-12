<?php

namespace VoidEngine;

class Component extends WFObject
{
    public $helpStorage = '';

    public function __construct ($className)
    {
        parent::__construct (
            is_string ($className) ?
                'System.Windows.Forms.'. substr ($className, ($pos = strrpos ($className, '\\')) !== false ? $pos + 1 : 0) : $className
        );
        
        Components::addComponent ($this->selector, $this);
    }
	
    public function __debugInfo (): array
    {
        return [
            'description' => $this->__toString (),
            'selector'    => $this->selector,
            'name'        => $this->name,
            'objectInfo'  => json_encode ($this, JSON_PRETTY_PRINT)
        ];
    }

    public function __unset ($name)
    {
        if (isset ($this->$name))
        {
            if (is_int ($this->$name))
            {
                Components::removeComponent ($this->$name);

                if (VoidEngine::objectExists ($this->$name))
                    VoidEngine::removeObjects ($this->$name);
            }

            elseif ($this->$name instanceof Component)
                $this->$name->dispose ();
        }

        unset ($this->$name);
    }

    public function dispose (): void
	{
        foreach (get_object_vars ($this) as $param => $value)
            if (isset ($this->$param))
            {
                if (is_int ($value))
                {
                    Components::removeComponent ($value);

                    if (VoidEngine::objectExists ($value))
                        VoidEngine::removeObjects ($value);
                }

                elseif ($value instanceof Items)
                {
                    $value->clear ();
                    
                    VoidEngine::removeObjects ($value->selector);
                }

                elseif ($value instanceof Component)
                    $value->dispose ();

                unset ($this->$param);
            }

        Components::cleanJunk ();
    }

    public function __destruct ()
    {
        if (VoidEngine::destructObject ($this->selector))
        {
            VoidEngine::removeObjects ($this->selector);
            Components::removeComponent ($this->selector);
        }
    }
}

?>
