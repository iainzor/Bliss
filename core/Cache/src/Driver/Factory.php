<?php
namespace Cache\Driver;

use Cache\Config;

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
	
	public function storageInstance()
	{
		$factoryClass = __NAMESPACE__ ."\\". ucfirst($this->name) ."\\StorageFactory";
		if (!class_exists($factoryClass)) {
			throw new \Exception("Driver factory '{$factoryClass}' could not be found");
		}
		
		$factory = new $factoryClass();
		if (!($factory instanceof StorageFactoryInterface)) {
			throw new \Exception("Class '{$factoryClass}' must implement \\Cache\\Driver\\StorageFactoryInterface");
		}
		
		return $factory->create($this->options);
	}
}