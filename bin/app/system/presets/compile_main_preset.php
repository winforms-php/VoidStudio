namespace VoidEngine;

%VoidEngine%

if (isset ($GLOBALS['__underConstruction']))
{
    $events = unserialize (gzinflate (base64_decode ('%events%')));

    foreach ($GLOBALS['__underConstruction'] as $group => $objects)
        foreach ($objects as $name => $selector)
        {
            $object = new WFObject ($selector);

            if (isset ($events[$group][$name]))
                foreach ($events[$group][$name] as $eventName => $event)
                {
                    Events::reserveObjectEvent ($selector, $eventName);
                    
                    VoidEngine::setObjectEvent ($selector, $eventName, "namespace VoidEngine;\n\n\$self = _c($selector);\n\$args = isset (\$args) ? (is_int (\$args) && VoidEngine::objectExists (\$args) ? new EventArgs (\$args) : \$args) : false;\n\n". $event);
                }

            try
            {
                $object->name = $name;
            }

            catch (\Throwable $e) {}

            Components::addComponent ($selector, $object);
        }

    $enteringPoint = $GLOBALS['__underConstruction']['%entering_point%']['%entering_point%'];
    unset ($GLOBALS['__underConstruction']);

    $APPLICATION->run ($enteringPoint);
}

else throw new \Exception ('Objects not initialized');