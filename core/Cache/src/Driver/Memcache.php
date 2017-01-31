<?php
namespace Cache\Driver;

class Memcache implements DriverInterface
{
	/**
	 * @var MemcacheServer[]
	 */
	private $servers = [];
	
	/**
	 * @var \Memcache
	 */
	private $instance;
	
	/**
	 * Constructor
	 * 
	 * @param MemcacheServer[] $servers
	 */
	public function __construct(array $servers)
	{
		$this->servers = $servers;
	}
	
	/**
	 * Get the memcache instance
	 * 
	 * @return \Memcache
	 */
	private function instance()
	{
		if (!$this->instance) {
			$this->instance = new \Memcache();
			
			foreach ($this->servers as $server) {
				$this->instance->addServer(
					$server->host(),
					$server->port(),
					$server->isPersistent(),
					$server->weight(),
					$server->timeout(),
					$server->retryInterval()
				);
			}
		}
		
		return $this->instance;
	}
	
	/**
	 * Get an item using its unique key
	 * 
	 * @param string $key
	 * @return mixed
	 */
	public function get($key) 
	{
		$contents = $this->instance()->get($key);
		if ($contents !== false) {
			return unserialize($contents);
		}
		return false;
	}
	
	/**
	 * Set a key's resource item
	 * 
	 * @param string $key
	 * @param \Cache\Resource $resource
	 * @return boolean
	 */
	public function set($key, \Cache\Resource $resource) 
	{
		return $this->instance()->set($key, serialize($resource->contents()), 0, $resource->lifetime());
	}
	
	/**
	 * Check if the memcache server is valid
	 * 
	 * @return boolean
	 */
	public function isValid() 
	{
		$valid = true;
		$previousHandler = set_error_handler(function($number, $message) use ($valid) {
			$valid = false;
		});
		try {
			$instance = $this->instance();
			$instance->getVersion();
		} catch (\Exception $ex) {
			$valid = false;
		}
		
		set_error_handler($previousHandler);
		
		return $valid;
	}
	
	/**
	 * @param string $key
	 * @param int $lifetime
	 * @return boolean
	 */
	public function isExpired($key, $lifetime) 
	{
		return $this->instance()->get($key) === false;
	}
}