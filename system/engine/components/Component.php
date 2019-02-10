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
            'objectInfo'  => json_encode ((array)($this), JSON_PRETTY_PRINT)
        ];
    }
	
	public function dispose (): void
	{
        if (isset ($this->selector) && is_int ($this->selector))
        {
            $this->callMethod ('Dispose');

            VoidEngine::removeObject ($this->selector);
        }

        foreach ((array)($this) as $param => $value)
            if (isset ($this->$param))
            {
                if (is_int ($value) && VoidEngine::objectExists ($value))
                    VoidEngine::removeObject ($value);

                elseif ($value instanceof Items)
                {
                    VoidEngine::removeObject (...$value->list);

                    $item->dispose ();
                }

                elseif ($value instanceof Component)
                    $value->dispose ();

                unset ($this->$param);
            }
    }
}

?>
