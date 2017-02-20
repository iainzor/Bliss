<?php
namespace Http;

class Route
{
	/**
	 * @var string
	 */
	private $module;
	
	/**
	 * @var string
	 */
	private $controller;
	
	/**
	 * @var string
	 */
	private $action;
	
	/**
	 * @var array
	 */
	private $params = [];
	
	/**
	 * Constructor
	 * 
	 * @param string $moduleName
	 * @param string $controllName
	 * @param string $actionName
	 * @param array $params
	 */
	public function __construct(
		string $moduleName, 
		string $controllName,
		string $actionName,
		array $params = []
	) {
		$this->module = $moduleName;
		$this->controller = $controllName;
		$this->action = $actionName;
		$this->params = $params;
	}
	
	/**
	 * Get the name of the module to execute for this route
	 * 
	 * @return string
	 */
	public function module() : string
	{
		return $this->module;
	}
	
	/**
	 * Get the name of the controller to execute for this route
	 * 
	 * @return string
	 */
	public function controller() : string
	{
		return $this->controller;
	}
	
	/**
	 * Get the name of the action to execute for this route
	 * 
	 * @return string
	 */
	public function action() : string
	{
		return $this->action;
	}
	
	/**
	 * Get any additional parameters assigned to the route
	 * 
	 * @return array
	 */
	public function params() : array
	{
		return $this->params;
	}
	
	/**
	 * Attempt to get the value of a parameter from the route.
	 * If the parameter cannot be found, the $defaultValue will be returned.
	 * 
	 * @param string $name
	 * @param mixed $defaultValue
	 * @return mixed
	 */
	public function param(string $name, $defaultValue = null)
	{
		return isset($this->params[$name])
			? $this->params[$name]
			: $defaultValue;
	}
}
