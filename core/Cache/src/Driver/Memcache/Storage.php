<?php
namespace Cache\Driver\Memcache;

use Cache\Driver\StorageInterface;

class Storage implements StorageInterface
{
	/**
	 * @var \Memcache
	 */
	private $memcache;
	
	/**
	 * @var int
	 */
	private $defaultLifetime = 30;
	
	/**
	 * Constructor
	 * 
	 * @param \Memcache $memcache
	 */
	public function __construct(\Memcache $memcache)
	{
		$this->memcache = $memcache;
	}
	
	/**
	 * Close the connection to the memcached server
	 */
	public function __destruct() 
	{
		$this->memcache->close();
	}
	
	/**
	 * Attempt to get cache from the memcached server
	 * 
	 * @param string $hash
	 * @return mixed
	 */
	public function get($hash) 
	{
		return $this->memcache->get($hash);
	}
	
	/**
	 * Delete cache from the memcached server
	 * 
	 * @param string $hash
	 * @return boolean
	 */
	public function delete($hash) 
	{
		return $this->memcache->delete($hash);
	}

	/**
	 * Add a resource's contents to the memcached server
	 * 
	 * @param string $hash
	 * @param mixed $contents
	 * @param int $expires Optional unix timestamp of when the content expires
	 * @return boolean
	 */
	public function put($hash, $contents, $expires = null) 
	{
		$expires = $expires === null ? $this->defaultLifetime : (int) $expires;
		$res = $this->memcache->set($hash, $contents, 0, $expires);
		
		return $res;
	}
}