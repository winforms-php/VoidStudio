<?php

/*
    Класс, отвечающий за портирование кода, необходимого для работы WindowsForms-ядра
*/

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
