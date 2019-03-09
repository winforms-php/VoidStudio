namespace VoidEngine;

%VoidEngine%

if (isset ($GLOBALS['__underConstruction']))
{
    foreach ($GLOBALS['__underConstruction'] as $group => $objects)
        foreach ($objects as $name => $selector)
        {
            $object = new Control (null, $selector);

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