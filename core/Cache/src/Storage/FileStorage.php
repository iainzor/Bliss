<?php
namespace Cache\Storage;

use Bliss\FileSystem\File;

class FileStorage implements StorageInterface
{
	/**
	 * @var string
	 */
	private $rootDir;
	
	/**
	 * Constructor
	 * 
	 * @param string $rootDir
	 */
	public function __construct($rootDir)
	{
		$this->rootDir = $rootDir;
	}
	
	/**
	 * Generate a pull file path using the storage's root directory
	 * 
	 * @param string $hash
	 * @return string
	 */
	public function path($hash)
	{
		return rtrim($this->rootDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $hash;
	}
	
	/**
	 * Delete a cache file
	 * 
	 * @param string $hash
	 * @return boolean
	 */
	public function delete($hash) 
	{
		return unlink($this->path($hash));
	}

	/**
	 * Attempt to load a cached file
	 * 
	 * @param string $hash
	 * @param \DateTime $expires An optional expiration time of the file
	 * @return mixed Returns FALSE if the file does not exist or is expired, otherwise it returns the file's contents
	 */
	public function get($hash, \DateTime $expires = null) 
	{
		$path = $this->path($hash);
		if (is_file($path)) {
			$modified = (int) filemtime($path);
			
			if (!isset($expires) || $modified < $expires->getTimestamp()) {
				return file_get_contents($path);
			}
		}
		return false;
	}

	/**
	 * Write contents to the file
	 * 
	 * @param string $hash
	 * @param string $contents
	 */
	public function put($hash, $contents) 
	{
		$file = new File($this->path($hash), $contents);
		$file->write();
	}
}