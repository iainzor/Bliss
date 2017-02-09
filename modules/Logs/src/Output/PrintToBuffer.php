<?php
namespace Logs\Output;

class PrintToBuffer implements OutputInterface
{
	private $includeTimestamp = true;
	
	public function next(\Logs\AbstractMessage $message) 
	{
		if ($this->includeTimestamp) {
			echo date("Y-m-d H:i:s") ."\t";
		}
		echo $message->text() ."\n";
	}
}
