<?php
namespace Cache\Driver;

use Cache\DriverConfig;

interface DriverInterface
{
	public function configure(DriverConfig $config);
	
	public function put(string $key, $content, int $expires);
	
	public function get(string $key);
}