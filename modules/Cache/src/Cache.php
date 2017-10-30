<?php
namespace Cache;

class Cache
{
	/**
	 * @var Driver\DriverInterface
	 */
	private $driver;
	
	/**
	 * @var \Cache\Config
	 */
	private $config;
	
	/**
	 * Constructor
	 * 
	 * @param \Cache\Config $config
	 * @param \Cache\Driver\DriverInterface $driver
	 */
	public function __construct(Config $config, Driver\DriverInterface $driver)
	{
		$this->config = $config;
		$this->driver = $driver;
	}
	
	/**
	 * Attempt to get a cache's content
	 * 
	 * @param string $key
	 * @return mixed
	 */
	public function get(string $key)
	{
		$item = $this->driver->get(
			$this->generateKey($key)
		);
		
		return $item->isExpired() ? false : $item->contents;
	}
	
	/**
	 * Put data into the cache 
	 * 
	 * @param string $key
	 * @param mixed $content
	 * @param int $lifetime Time in seconds until the item is considered expired
	 */
	public function put(string $key, $content, int $lifetime = null)
	{
		$item = new CacheItem(
			$this->generateKey($key), 
			$content
		);
		
		if ($lifetime !== null) {
			$expires = new \DateTime();
			$expires->setTimestamp(
				time() + $lifetime	
			);
			$item->setExpires($expires);
		}
		
		$this->driver->put($item);
	}
	
	/**
	 * Generate a new hashed key using a provided key string and the cache's current
	 * namespace
	 * 
	 * @param string $key
	 * @return string
	 */
	public function generateKey(string $key) : string
	{
		return md5($this->config->cacheNamespace() ."--". $key);
	}
}