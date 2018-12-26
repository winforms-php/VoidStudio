<?php

/*
    Класс, отвечающий за портирование кода, необходимого для работы WindowsForms-ядра
*/

VoidEngine::loadModule ('WFCompiler.dll');

class WinFormsException extends Exception
{
	public function __construct (string $message, string $file, int $line)
	{
		$this->file = $file;
		$this->line = $line;

		parent::__construct ($message);
	}
}

?>
