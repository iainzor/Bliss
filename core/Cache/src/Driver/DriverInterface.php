<?php
namespace Cache\Driver;

use Cache\Resource;

interface DriverInterface
{
	public function get($key);
	
	public function set($key, Resource $resource);
	
	public function isValid();
	
	public function isExpired($key, $lifetime);
}