<?php

namespace VoidEngine;

class Component extends WFObject
{
    public $helpStorage = '';

    public function __construct (string $className)
    {
        $className = substr ($className, ($pos = strrpos ($className, '\\')) !== false ? $pos + 1 : 0);

        parent::__construct ('System.Windows.Forms.'. $className);
        Components::addComponent ($this->selector, $this);
    }

    function __toString (): string
    {
        return $this->callMethod ('ToString');
    }
	
    public function __debugInfo (): array
    {
        return [
            'description' => $this->callMethod ('ToString'),
            'objectInfo'  => json_encode ($this, JSON_PRETTY_PRINT)
        ];
    }

    public function __unset ($name)
    {
        if (isset ($this->$name))
        {
            if (is_int ($this->$name) && VoidEngine::objectExists ($this->$name))
                VoidEngine::removeObjects ($this->$name);

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
                if (is_int ($value) && VoidEngine::objectExists ($value))
                    VoidEngine::removeObjects ($value);

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

    // TODO: более строгие правила очистки мусорных объектов
    public function __destruct ()
    {
        if (isset ($this->selector))
            VoidEngine::destructObjects ($this->selector);
    }
}

?>
