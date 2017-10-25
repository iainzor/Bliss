<?php
namespace Cache\Driver;

use Cache\CacheItem;

class Memcached implements DriverInterface 
{
	const DEFAULT_PORT = 11211;
	
	const CONFIG_SERVER_POOL = "memcached.config.serverPool";
	const CONFIG_SERVER_HOST = "memcached.config.server.host";
	const CONFIG_SERVER_PORT = "memcached.config.server.port";
	
	/**
	 * @var \Memcache
	 */
	private $memcache;
	
	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->memcache = new \Memcache();
	}
	
	public function configure(\Cache\DriverConfig $config) 
	{
		$pool = $config->get(self::CONFIG_SERVER_POOL, []);
		
		foreach ($pool as $server) {
			$this->addServer($server);
		}
	}
	
	public function addServer(array $config) : bool
	{
		if (!isset($config[self::CONFIG_SERVER_HOST])) {
			throw new \Exception("Server config is missing a host name");
		}
		
		$host = $config[self::CONFIG_SERVER_HOST];
		$port = isset($config[self::CONFIG_SERVER_PORT]) ? $config[self::CONFIG_SERVER_PORT] : 11211;
		
		return $this->memcache->addServer($host, $port);
	}

	public function get(string $key): CacheItem 
	{
		$contents = $this->memcache->get($key);
		if ($contents !== false) {
			return unserialize($contents);
		}
		
		return new CacheItem($key);
	}

	public function put(CacheItem $item) 
	{
		$lifetime = isset($item->expires) ? $item->expires->getTimestamp() - time() : 0;
		$this->memcache->set(
			$item->key,
			serialize($item),
			MEMCACHE_COMPRESSED,
			$lifetime
		);
	}
}