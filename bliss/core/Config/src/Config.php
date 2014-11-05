<?php
namespace Config;

class Config
{
	/**
	 * @var array
	 */
	private $data = [];
	
	/**
	 * Constructor
	 * 
	 * @param array $data
	 */
	public function __construct(array $data = [])
	{
		foreach ($data as $key => $value) {
			if (is_array($value)) {
				$keys = array_keys($value);
				
				if (empty($keys) || !is_numeric($keys[0])) {
					$value = new self($value);
				}
			}
			
			$this->data[$key] = $value;
		}
	}
	
	/**
	 * Set a config value
	 * 
	 * @param string $namespace
	 * @param mixed $value
	 */
	public function set($name, $value)
	{
		if (is_array($value)) {
			$keys = array_keys($value);
			
			if (empty($keys) || !is_numeric($keys[0])) {
				$value = new self($value);
			}
		}
		
		$this->data[$name] = $value;
	}
	
	/**
	 * Get a value from the configuration object
	 * 
	 * @param string $name
	 * @param mixed $defaultValue
	 * @return mixed
	 */
	public function get($name, $defaultValue = [])
	{
		$value = isset($this->data[$name]) ? $this->data[$name] : $defaultValue;
		
		return $value;
	}
	
	public function __get($name) 
	{
		return $this->get($name);
	}
	
	public function __set($name, $value) 
	{
		$this->set($name, $value);
	}
}