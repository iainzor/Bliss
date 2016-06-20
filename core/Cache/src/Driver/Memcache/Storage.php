<?php
namespace Cache\Driver\Memcache;

use Cache\Driver\StorageInterface,
	Cache\Resource\ResourceInterface;

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
		unset($this->memcache);
	}
	
	/**
	 * Attempt to get cache from the memcached server
	 * 
	 * @param string $hash
	 * @return mixed
	 */
	public function get($hash) 
	{
		/* @var $resource ResourceInterface */
		$resource = $this->memcache->get($hash);
		if ($resource) {
			return $resource->contents();
		}
		return false;
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
	 * @param ResourceInterface $resource
	 * @return boolean
	 */
	public function put($hash, ResourceInterface $resource) 
	{
		$expires = $resource->expires() ? $resource->expires() : $this->defaultLifetime;
		$res = $this->memcache->set($hash, $resource, 0, $expires);
		
		return $res;
	}
}