<?php
namespace Cache\Tests;

use Cache\Storage\StorageInterface;

class MockStorage implements StorageInterface
{
	private $cache = [];
	
	public function get($hash) 
	{
		$cache = array_key_exists($hash, $this->cache) ? $this->cache[$hash] : false;
		
		return $cache;
	}

	public function put($hash, $contents) 
	{
		$this->cache[$hash] = $contents;
	}
	
	public function delete($hash) {
		unset($this->cache[$hash]);
	}

}