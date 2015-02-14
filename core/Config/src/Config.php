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
		$this->setData($data);
	}
	
	/**
	 * Get or set the configuration's data
	 * 
	 * @param array $data
	 * @return array
	 */
	public function data(array $data = null)
	{
		if ($data !== null) {
			$this->setData($data);
		}
		return $this->data;
	}
	
	/**
	 * Set the data of the config
	 * 
	 * @param array $data
	 */
	public function setData(array $data)
	{
		$this->data = [];
		
		foreach ($data as $name => $value) {
			$this->set($name, $value);
		}
	}
	
	/**
	 * Set a config value
	 * 
	 * @param string $name
	 * @param mixed $value
	 */
	public function set($name, $value)
	{
		if (is_array($value)) {
			if (empty($value)) {
				$value = new self();
			} else {
				$keys = array_keys($value);

				if (empty($keys) || !is_numeric($keys[0])) {
					$value = new self($value);
				}
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
	
	/**
	 * Recursively merge the config with an array 
	 * 
	 * @param array $data
	 */
	public function merge(array $data)
	{
		foreach ($data as $name => $value) {
			$existing = $this->get($name);
			
			if ($existing) {
				$this->mergeExisting($name, $value);
			} else {
				$this->set($name, $value);
			}
		}
	}
	
	/**
	 * Merge data with an existing value in the config
	 * 
	 * @param string $name
	 * @param mixed $newValue
	 */
	private function mergeExisting($name, $newValue)
	{
		$value = $this->get($name);
		
		if ($value instanceof Config && is_array($newValue)) {
			$value->merge($newValue);
		} else if (is_array($value)) {
			$newValue = array_merge($value, $newValue);
			$this->set($name, $newValue);
		} else {
			$this->set($name, $newValue);
		}
	}
	
	public function __get($name) 
	{
		return $this->get($name);
	}
	
	public function __set($name, $value) 
	{
		$this->set($name, $value);
	}
	
	public function toArray()
	{
		$data = [];
		foreach ($this->data as $name => $value) {
			if ($value instanceof Config) {
				$data[$name] = $value->toArray();
			} else {
				$data[$name] = $value;
			}
			
			if (is_array($data[$name]) && empty($data[$name])) {
				unset($data[$name]);
			}
		}
		return $data;
	}
}