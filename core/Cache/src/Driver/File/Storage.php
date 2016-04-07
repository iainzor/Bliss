<?php
namespace Cache\Driver\File;

use Cache\Driver\StorageInterface,
	Cache\Resource\ResourceInterface,
	Bliss\FileSystem\File;

class Storage implements StorageInterface
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
	 * @return mixed Returns FALSE if the file does not exist or is expired, otherwise it returns the file's contents
	 */
	public function get($hash) 
	{
		$path = $this->path($hash);
		if (is_file($path)) {
			/* @var $resource ResourceInterface */
			$resource = unserialize(file_get_contents($path));
			$modified = filemtime($path);
			$age = time() - $modified;
			
			if ($age < $resource->expires()) {
				return $resource->contents();
			} else {
				unlink($path);
			}
		}
		return false;
	}

	/**
	 * Write contents to the file
	 * 
	 * @param string $hash
	 * @param ResourceInterface $resource
	 */
	public function put($hash, ResourceInterface $resource) 
	{
		$filepath = $this->path($hash);
		$file = new File($filepath, serialize($resource));
		$file->write();
	}
}