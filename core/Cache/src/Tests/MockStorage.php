<?php
namespace Cache\Tests;

use Cache\Driver\StorageInterface,
	Cache\Resource\ResourceInterface;

class MockStorage implements StorageInterface
{
	private $cache = [];
	
	public function get($hash, \DateTime $expires = null) 
	{
		$cache = array_key_exists($hash, $this->cache) ? $this->cache[$hash] : false;
		
		return $cache;
	}

	public function put($hash, ResourceInterface $resource) 
	{
		$this->cache[$hash] = $resource;
	}
	
	public function delete($hash) {
		unset($this->cache[$hash]);
	}

}