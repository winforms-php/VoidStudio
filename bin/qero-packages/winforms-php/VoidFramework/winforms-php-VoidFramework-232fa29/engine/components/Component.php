<?php

namespace VoidEngine;

class Component extends WFObject
{
    public $helpStorage = '';

    public $class     = 'System.Windows.Forms.Component';
    public $namespace = 'System.Windows.Forms';

    public function __construct ($className = null, ...$args)
    {
        parent::__construct (
            $className === null ? $this->class : $className,
            $this->namespace, ...$args
        );
        
        Components::addComponent ($this->selector, $this);
    }
	
    public function __debugInfo (): array
    {
        return [
            'description' => @$this->__toString (),
            'selector'    => @$this->selector,
            'name'        => @$this->name,
            'objectInfo'  => @json_encode ($this, JSON_PRETTY_PRINT)
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
        foreach (array_diff (get_object_vars ($this), ['selector']) as $param => $value)
        {
            if (is_int ($value))
            {
                Components::removeComponent ($value);

                if (VoidEngine::objectExists ($value))
                    VoidEngine::removeObjects ($value);
            }

            elseif ($value instanceof Component)
                $value->dispose ();

            unset ($this->$param);
        }

        if (isset ($this->selector))
        {
            if (VoidEngine::objectExists ($this->selector))
                VoidEngine::removeObjects ($this->selector);
            
            Components::removeComponent ($this->selector);
        }

        Components::cleanJunk ();
    }

    public function __destruct ()
    {
        if (isset ($this->selector) && VoidEngine::destructObject ($this->selector))
        {
            VoidEngine::removeObjects ($this->selector);
            Components::removeComponent ($this->selector);

            gc_collect_cycles ();
        }
    }
}
