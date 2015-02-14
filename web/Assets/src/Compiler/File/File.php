<?php
namespace Assets\Compiler\File;

class File
{
	/**
	 * @var string
	 */
	private $contents;
	
	/**
	 * Constructor
	 * 
	 * @param string $contents
	 */
	public function __construct($contents)
	{
		$this->contents = $contents;
	}
	
	/**
	 * Get the contents of the file
	 * 
	 * @return string
	 */
	public function contents()
	{
		return $this->contents;
	}
	
	/**
	 * Check if the file is empty
	 * 
	 * @return boolean
	 */
	public function isEmpty()
	{
		return strlen(trim($this->contents)) === 0;
	}
	
	/**
	 * Save the file to the specified path
	 * 
	 * If the directory does not exist, this will attempt to create it
	 * 
	 * @param string $path
	 */
	public function save($path)
	{
		$dirname = pathinfo($path, PATHINFO_DIRNAME);
		
		if (!is_dir($dirname)) {
			$mask = umask(0);
			$result = @mkdir($dirname, 0777, true);
			umask($mask);
			
			if ($result === false) {
				throw new \Exception("Could not create directory: {$dirname}");
			}
		}
		
		file_put_contents($path, $this->contents);
	}
}