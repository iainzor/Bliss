<?php
namespace Cache\Driver;

use Cache\Config,
	Bliss\App\Container as AppContainer;

class Factory
{
	/**
	 * @var string
	 */
	private $name;
	
	/**
	 * @var array
	 */
	private $options = [];
	
	/**
	 * Constructor
	 * 
	 * @param array $config
	 */
	public function __construct(array $config)
	{
		if (!isset($config[Config::DRIVER_NAME])) {
			throw new \Exception("No driver name was provided in the configuration");
		}
		
		$this->name = $config[Config::DRIVER_NAME];
		$this->options = isset($config[Config::DRIVER_OPTIONS]) ? $config[Config::DRIVER_OPTIONS] : [];
	}
	
	/**
	 * Generate a new storage instance
	 * 
	 * @param \Bliss\App\Container $app
	 * @return StorageInterface
	 * @throws \Exception
	 */
	public function storageInstance(AppContainer $app)
	{
		$factoryClass = __NAMESPACE__ ."\\". ucfirst($this->name) ."\\StorageFactory";
		if (!class_exists($factoryClass)) {
			throw new \Exception("Driver factory '{$factoryClass}' could not be found");
		}
		
		$factory = new $factoryClass();
		if (!($factory instanceof StorageFactoryInterface)) {
			throw new \Exception("Class '{$factoryClass}' must implement \\Cache\\Driver\\StorageFactoryInterface");
		}
		
		$storage = $factory->create($app, $this->options);
		if (!($storage instanceof StorageInterface)) {
			throw new \Exception("Storage instance returned from {$factoryClass}::create() must be an instance of StorageInterface");
		}
		return $storage;
	}
}