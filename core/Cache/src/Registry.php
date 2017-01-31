<?php
namespace Cache;

class Registry
{
	/**
	 * @var Driver\DriverInterface
	 */
	private $driver;
	
	/**
	 * @var Resource[]
	 */
	private $resources = [];
	
	/**
	 * Constructor
	 * 
	 * @param Driver\DriverInterface $driver
	 */
	public function __construct(Driver\DriverInterface $driver)
	{
		$this->driver = $driver;
	}
	
	/**
	 * Get a resource instance matching the supplied arguments
	 * 
	 * @param string $resourceName
	 * @param mixed $resourceId
	 * @param array $params
	 * @param int $lifetime
	 * @return Resource
	 */
	public function resource($resourceName, $resourceId, array $params = [], $lifetime = 30)
	{
		$key = $this->key($resourceName, $resourceId, $params);
		if (!isset($this->resources[$key])) {
			$this->resources[$key] = new Resource($this->driver, $key, $lifetime);
		}
		return $this->resources[$key];
	}
	
	/**
	 * Generate a unique key name for the set of arguments
	 * 
	 * @param string $resourceName
	 * @param mixed $resourceId
	 * @param array $params
	 * @return string
	 */
	private function key($resourceName, $resourceId, array $params)
	{
		return implode("-", [
			substr(md5($resourceName), 0, 10),
			substr(md5($resourceId), 0, 10),
			substr(md5(json_encode($params)), 0, 10)
		]);
	}
}