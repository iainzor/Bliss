<?php
namespace Logs\Output;

use Logs\Message\AbstractMessage;

class PrintToBuffer implements OutputInterface
{
	private $includeTimestamp = true;
	
	public function next(AbstractMessage $message) 
	{
		if ($this->includeTimestamp) {
			echo date("Y-m-d H:i:s") ."\t";
		}
		echo $message->text() ."\n";
	}
}
