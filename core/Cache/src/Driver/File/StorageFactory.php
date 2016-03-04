<?php
namespace Cache\Driver\File;

use Cache\Driver\StorageFactoryInterface,
	Bliss\App;

class StorageFactory implements StorageFactoryInterface
{
	public function create(App\Container $app, array $options) 
	{
		if (!isset($options[Config::DIRECTORY])) {
			throw new \Exception("No file directory provided for cache storage");
		}
		
		$rootDir = $app->resolvePath("files/". $options[Config::DIRECTORY]);
		$storage = new Storage($rootDir);
		
		return $storage;
	}
}