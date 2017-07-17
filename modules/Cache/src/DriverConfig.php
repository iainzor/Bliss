<?php
namespace Cache;

class DriverConfig
{
	/**
	 * @var array
	 */
	protected $params = [];
	
	/**
	 * Constructor
	 * 
	 * @param array $params
	 */
	public function __construct(array $params)
	{
		$this->params = $params;
	}
	
	/**
	 * Get a parameter value from the configuration
	 * 
	 * @param string $name
	 * @param mixed $defaultValue
	 * @return mixed
	 */
	public function get(string $name, $defaultValue = null)
	{
		return isset($this->params[$name]) ? $this->params[$name] : $defaultValue;
	}
}
