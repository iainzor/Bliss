<?php
namespace Logs\Output;

class WriteToFile implements OutputInterface
{
	/**
	 * @var string
	 */
	private $filename;
	
	/**
	 * Constructor
	 * 
	 * @param string $filename
	 * @param int $filePermissions
	 * @throws \Exception
	 */
	public function __construct(string $filename, int $filePermissions = 0777)
	{
		if (!is_file($filename)) {
			$dir = dirname($filename);
			
			if (!is_dir($dir)) {
				if (!mkdir($dir, 0777, true)) {
					throw new \Exception("Cannot make or find file directory: {$dir}");
				}
			}
		}
		
		$this->filename = $filename;
	}
	
	/**
	 * Write a message to the file
	 * 
	 * @param \Logs\Message\AbstractMessage $message
	 */
	public function next(\Logs\Message\AbstractMessage $message) 
	{
		$handle = fopen($this->filename, "a");
		$line = date("Y-m-d H:i:s") ."\t". $message->text() ."\r\n";
		
		fputs($handle, $line);
		fclose($handle);
	}
}