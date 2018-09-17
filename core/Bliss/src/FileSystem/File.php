<?php
namespace Bliss\FileSystem;

use Bliss\Component;

class File extends Component 
{
	/**
	 * @var string
	 */
	private $filename;
	
	/**
	 * @var string
	 */
	private $contents;
	
	/**
	 * Constructor
	 * 
	 * @param string $filename
	 * @param null $contents
	 */
	public function __construct($filename, $contents = null)
	{
		$this->filename = $filename;
		$this->contents = $contents;
	}
	
	/**
	 * Get the full path to the file
	 * 
	 * @return string
	 */
	public function path()
	{
		return $this->filename;
	}
	
	/**
	 * Check if the file exists
	 * 
	 * @return boolean
	 */
	public function exists()
	{
		return is_file($this->filename);
	}
	
	/**
	 * Get or set the contents of the file
	 * 
	 * @param string $contents
	 * @return string
	 */
	public function contents($contents = null)
	{
		if ($contents !== null) {
			$this->contents = $contents;
		}
		return $this->contents;
	}
	
	/**
	 * Attempt to load the contents of the file
	 * 
	 * @return \Bliss\FileSystem\File
	 */
	public function load()
	{
		if (is_file($this->filename)) {
			$this->contents = file_get_contents($this->filename);
		}
		
		return $this;
	}
	
	/**
	 * Attempt to write the file
	 * 
	 * @param string $contents
	 * @param int $mode
	 * @throws Exception
	 */
	public function write($contents = null, $mode = 0777)
	{
		$contents = $this->contents($contents);
		$dir = dirname($this->filename);
		if (!is_dir($dir)) {
			if (!@mkdir($dir, $mode, true)) {
				throw new Exception("Could not create directory: {$dir}");
			}
		}
		
		if (is_file($this->filename) && !is_writable($this->filename)) {
			throw new Exception("File cannot be written to: {$this->filename}");
		}
		
		if (file_put_contents($this->filename, $contents) === false) {
			throw new Exception("Could not write to file: {$this->filename}");
		}
	}
	
	/**
	 * Delete the file
	 * 
	 * @return boolean
	 */
	public function delete()
	{
		if ($this->exists()) {
			return unlink($this->filename);
		}
	}
}