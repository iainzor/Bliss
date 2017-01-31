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
	 * Get or set the registry used to load and save cache resources
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
	 * Set the driver used to load and save cache resources. Setting this will
	 * reinstantiate the cache registry instance
	 * 
	 * @param Driver\DriverInterface $driver
	 */
	public function driver(Driver\DriverInterface $driver)
	{
		$this->registry = new Registry($driver);
	}
}