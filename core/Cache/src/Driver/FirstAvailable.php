<?php
namespace Cache\Driver;

use Cache\Resource;

class FirstAvailable implements DriverInterface 
{
	/**
	 * @var DriverInterface[]
	 */
	private $drivers = [];
	
	/**
	 * @var DriverInterface
	 */
	private $activeDriver;
	
	/**
	 * Constructor
	 * 
	 * @param DriverInterface[] $drivers
	 */
	public function __construct(array $drivers)
	{
		foreach ($drivers as $driver) {
			$this->addDriver($driver);
		}
	}
	
	/**
	 * Add a driver instance to the collection
	 * 
	 * @param DriverInterface $driver
	 */
	public function addDriver(DriverInterface $driver)
	{
		$this->drivers[] = $driver;
	}
	
	/**
	 * Attempt to get the first available cache driver
	 * 
	 * @return DriverInterface
	 * @throws \Exception
	 */
	private function driver()
	{
		if (!$this->activeDriver) {
			foreach ($this->drivers as $driver) {
				if ($driver->isValid()) {
					$this->activeDriver = $driver;
					break;
				}
			}
			
			if (!$this->activeDriver) {
				throw new \Exception("Could not find a suitable cache driver");
			}
		}
		return $this->activeDriver;
	}
	
	/**
	 * Check if the active driver is valid
	 * 
	 * @return boolean
	 */
	public function isValid()
	{
		try {
			return $this->driver()->isValid();
		} catch (\Exception $ex) {
			return false;
		}
	}
	
	/**
	 * Get the contents of a resource using its unique key
	 * 
	 * @param string $key
	 * @return mixed
	 */
	public function get($key) 
	{
		return $this->driver()->get($key);
	}
	
	/**
	 * Set the resource associated to the key provided
	 * 
	 * @param string $key
	 * @param Resource $resource 
	 * @return boolean
	 */
	public function set($key, Resource $resource) 
	{
		return $this->driver()->set($key, $resource);
	}
	
	/**
	 * @param string $key
	 * @param int $lifetime
	 * @return boolean
	 */
	public function isExpired($key, $lifetime) 
	{
		return $this->driver()->isExpired($key, $lifetime);
	}
}