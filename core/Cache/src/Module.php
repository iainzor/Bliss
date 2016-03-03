<?php
namespace Cache;

use Bliss\Module\AbstractModule;

class Module extends AbstractModule
{
	/**
	 * @var Registry
	 */
	private $registry;
	
	/**
	 * Get or set the registry used to load and save cache
	 * 
	 * @param \Cache\Registry $registry
	 * @return \Cache\Registry
	 */
	public function registry(Registry $registry = null)
	{
		if ($registry !== null) {
			$this->registry = $registry;
		}
		if (!$this->registry) {
			throw new \Exception("No cache registry has been created");
		}
		return $this->registry;
	}
	
	/**
	 * Set the cache driver options and create the registry
	 * 
	 * @param array $config
	 */
	public function driver(array $config)
	{
		$factory = new Driver\Factory($config);
		$storage = $factory->storageInstance();
		
		$this->registry(
			new Registry($storage)
		);
	}
}