<?php
namespace CmdLine;

require_once dirname(dirname(__DIR__)) ."/Core/src/AbstractApplication.php";

class Application extends \Core\AbstractApplication
{
	public function bootstrap() 
	{
		$this->di()->register($this);
	}
}
