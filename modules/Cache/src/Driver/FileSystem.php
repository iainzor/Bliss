<?php
namespace Cache\Driver;

use Cache\CacheItem,
	Cache\DriverConfig;

class FileSystem implements DriverInterface 
{
	const CONFIG_ROOTDIR = "fileSystem.config.rootDir";
	
	/**
	 * @var string
	 */
	private $rootDir;
	
	/**
	 * @param DriverConfig $config
	 * @throws \Exception
	 */
	public function configure(DriverConfig $config)
	{
		$this->rootDir = $config->get(self::CONFIG_ROOTDIR);
		
		if (!is_writable($this->rootDir)) {
			throw new \Exception("File cache root directory is not writable");
		}
	}
	
	/**
	 * @param string $key
	 * @return mixed
	 */
	public function get(string $key) : CacheItem
	{
		$path = $this->filepath($key);
		$item = new CacheItem($key, null, new \DateTime("yesterday"));
		
		if (is_file($path)) {
			$raw = file_get_contents($path);
			$cached = unserialize($raw);
			
			if (!($cached instanceof CacheItem) || $cached->isExpired()) {
				unset($path);
			} else {
				$item = $cached;
			}
		}
		
		return $item;
	}

	/**
	 * @param string $key
	 * @param mixed $data
	 * @param int $expires
	 */
	public function put(CacheItem $item) 
	{
		$path = $this->filepath($item->key);
		$raw = serialize($item);
		
		file_put_contents($path, $raw);
	}
	
	/**
	 * @param string $key
	 * @return string
	 */
	private function filepath(string $key) : string
	{
		return $this->rootDir . DIRECTORY_SEPARATOR .  md5($key);
	}
}