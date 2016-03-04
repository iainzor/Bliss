<?php
namespace Cache\Driver\Memcache;

use Cache\Driver\StorageFactoryInterface,
	Bliss\App;

class StorageFactory implements StorageFactoryInterface
{
	/**
	 * @param App\Container $app
	 * @param array $options
	 * @return \Cache\Driver\Memcache\Storage
	 * @throws \Exception
	 */
	public function create(App\Container $app, array $options) 
	{
		if (!isset($options[Config::HOST])) {
			throw new \Exception("No host provided for memcache driver");
		}
		
		$host = $options[Config::HOST];
		$port = isset($options[Config::PORT]) ? $options[Config::PORT] : 11211;
		$timeout = isset($options[Config::TIMEOUT]) ? $options[Config::TIMEOUT] : 1;
		$memcache = new \Memcache();
		$memcache->addserver($host, $port, true, 1, $timeout, 15, true, function($host, $port) {
			throw new \Exception("Could not connect to Memcache host at {$host}:{$port}");
		});
		
		return new Storage($memcache);
	}
}