<?php
namespace Core;

class ActionParams
{
	/**
	 * @var array
	 */
	private $params = [];
	
	/**
	 * Constructor
	 * 
	 * @param array $params
	 */
	public function __construct(array $params = [])
	{
		$this->params = $params;
	}
	
	/**
	 * Get a parameter by its name.  If it cannot be found, the $defaultValue 
	 * will be returned
	 * 
	 * @param string $name
	 * @param mixed $defaultValue
	 * @return mixed
	 */
	public function get(string $name, $defaultValue = null)
	{
		return isset($this->params[$name]) ? $this->params[$name] : $defaultValue;
	}
	
	/**
	 * Get all parameters accessible to the action
	 * 
	 * @return array
	 */
	public function getAll() : array
	{
		return $this->params;
	}
}