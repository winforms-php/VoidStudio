<?php

/*
    Класс, отвечающий за портирование кода, необходимого для работы WindowsForms-ядра
*/

$GLOBALS['__message_handler'] = function ($error)
{
    throw new \Exception ($error);
};

?>
