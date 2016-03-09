<?php
namespace Cache\Driver\File;

use Cache\Driver\StorageInterface,
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
		$datapath = $path .".metadata";
		if (is_file($path) && is_file($datapath)) {
			$modified = (int) filemtime($path);
			$metadata = unserialize(file_get_contents($datapath));
			$expires = $modified + $metadata["expires"];
			
			if ($expires < time()) {
				return unserialize(file_get_contents($path));
			} else {
				unlink($path);
				unlink($datapath);
			}
		}
		return false;
	}

	/**
	 * Write contents to the file
	 * 
	 * @param string $hash
	 * @param string $contents
	 * @param int $expires
	 */
	public function put($hash, $contents, $expires = null) 
	{
		$filepath = $this->path($hash);
		$file = new File($filepath, serialize($contents));
		$file->write();
		
		$datapath = $filepath .".metadata";
		$metadata = [
			"expires" => $expires
		];
		$data = new File($datapath, serialize($metadata));
		$data->write();
	}
}