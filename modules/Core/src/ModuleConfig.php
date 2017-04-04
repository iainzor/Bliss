<?php
namespace Core;

class ModuleConfig
{
	/**
	 * @var ModuleDefinition
	 */
	private $module;
	
	/**
	 * @var array
	 */
	private $data = [];
	
	/**
	 * Constructor
	 * 
	 * @param array $data
	 */
	public function __construct(ModuleDefinition $module, array $data = [])
	{
		$this->module = $module;
		$this->data = $data;
	}
	
	/**
	 * Populate the configuration data
	 * 
	 * @param array $data
	 */
	public function populate(array $data)
	{
		$this->data = $data;
	}
	
	/**
	 * Attempt to get a configuration value.  If the name is not found, the 
	 * $defaultValue will be returned.
	 * 
	 * @param string $name
	 * @param mixed $defaultValue
	 * @return mixed
	 */
	public function get(string $name, $defaultValue = null)
	{
		return isset($this->data[$name]) ? $this->data[$name] : $defaultValue;
	}
}
