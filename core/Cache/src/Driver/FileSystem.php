<?php
namespace Cache\Driver;

class FileSystem implements DriverInterface 
{
	private $cacheDir = "/";
	
	/**
	 * Constructor
	 * 
	 * @param string $cacheDir
	 */
	public function __construct($cacheDir)
	{
		$this->cacheDir = $cacheDir;
	}
	
	/**
	 * Attempt to get the cache contents
	 * 
	 * @param string $key
	 * @return mixed
	 */
	public function get($key) 
	{
		$path = $this->resolve($key);
		if (!is_file($path)) {
			return false;
		}
		return unserialize(
			file_get_contents($path)
		);
	}

	/**
	 * Set the contents of cache key
	 * 
	 * @param string $key
	 * @param \Cache\Resource $resource
	 * @return int
	 */
	public function set($key, \Cache\Resource $resource) 
	{
		$path = $this->resolve($key);
		return file_put_contents($path, serialize($resource->contents()));
	}

	public function isValid() 
	{
		if (!is_dir($this->cacheDir)) {
			mkdir($this->cacheDir, 0777, true);
		}
		return is_dir($this->cacheDir);
	}
	
	/**
	 * Check if the file cache is expired
	 * 
	 * @param string $key
	 * @param int $lifetime
	 * @return boolean
	 */
	public function isExpired($key, $lifetime) 
	{
		$path = $this->resolve($key);
		
		if (!is_file($path)) {
			return true;
		}
		
		$modified = filemtime($path);
		$now = time();
		$expires = $now - $lifetime;
		
		return $expires >= $modified;
	}
	
	/**
	 * Resolve the full path to the file used to store data for a key
	 * 
	 * @param string $key
	 * @return string
	 */
	private function resolve($key)
	{
		return rtrim($this->cacheDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR ."/". $key;
	}
}