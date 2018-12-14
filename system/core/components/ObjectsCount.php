<?php

namespace VoidEngine;

class ObjectsCount
{
    protected $shape;

    public function __construct (Control $object = null)
    {
        if ($object !== null)
        {
            $this->shape = new Panel (Components::getComponent ($object->parent));

            $this->shape->bounds = [
                $object->x - 1,
                $object->y - 1,
                $object->w + 2,
                $object->h + 2
            ];

            $this->shape->toBack ();
        }
    }

    public function count (Control $object)
    {
        $parent = $object->parent;

        if (!isset ($this->shape) || $this->shape->parent != $parent)
            $shape = new Panel ($parent);

        $shape->bounds = [
            $object->x - 2,
            $object->y - 2,
            $object->w + 4,
            $object->h + 4
        ];

        $shape->backgroundColor = clBlack;
        $shape->toBack ();

        $object->MouseDownEvent = function ($self, $args)
        {
            $mouse = new MouseEventArgs ($args);

            $self->helpStorage = [$mouse->x, $mouse->y];
        };

        $object->MouseUpEvent = function ($self)
        {
            $self->helpStorage = '';
        };

        $object->MouseMoveEvent = function ($self, $args) use ($shape)
        {
            if (is_array ($self->helpStorage))
            {
                $mouse = new MouseEventArgs ($args);

                $self->x += $mouse->x - $self->helpStorage[0];
                $self->y += $mouse->y - $self->helpStorage[1];

                $shape->location = $self->location;
            }
        };

        $this->shape = $shape;
    }
}

?>
