<?php
namespace Cache\Driver;

use Cache\CacheItem,
	Cache\DriverConfig;

interface DriverInterface
{
	public function configure(DriverConfig $config);
	
	public function put(CacheItem $item);
	
	public function get(string $key) : CacheItem;
}