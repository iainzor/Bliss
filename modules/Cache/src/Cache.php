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
		return $this->driver->get($key);
	}
	
	/**
	 * Put data into the cache 
	 * 
	 * @param string $key
	 * @param mixed $content
	 * @param int $expires
	 */
	public function put(string $key, $content, int $expires = null)
	{
		if ($expires === null) {
			$expires = time() + $this->config->defaultLifetime();
		}
		
		$this->driver->put($key, $content, $expires);
	}
}