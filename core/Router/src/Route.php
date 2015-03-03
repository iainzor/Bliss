<?php
namespace Router;

class Route
{
	const PARAM_MODULE = "module";
	const PARAM_CONTROLLER = "controller";
	const PARAM_ACTION = "action";
	
	/**
	 * @var string
	 */
	private $route;
	
	/**
	 * @var array
	 */
	private $matchNames = [];
	
	/**
	 * @var array
	 */
	private $defaultValues = [
		"controller" => "index",
		"action" => "index"
	];
	
	/**
	 * @var int
	 */
	private $priority = 1;
	
	/**
	 * @var array
	 */
	private $params = [];
	
	/**
	 * @var boolean
	 */
	private $isActive = true;
	
	/**
	 * Constructor
	 * 
	 * @param string $route
	 * @param array $matchValues
	 * @param array $defaultValues
	 * @param int $priority
	 */
	public function __construct($route = null, array $matchNames = [], array $defaultValues = [], $priority = 1)
	{
		$this->route = $route;
		$this->matchNames = $matchNames;
		$this->defaultValues = array_merge($this->defaultValues, $defaultValues);
		$this->priority = (int) $priority;
	}
	
	/**
	 * Get the route expression
	 * 
	 * @return string
	 */
	public function route()
	{
		return $this->route;
	}
	
	/**
	 * Get the priority of the route
	 * 
	 * @return int
	 */
	public function priority()
	{
		return $this->priority;
	}
	
	/**
	 * Check if the route matches the test string provided
	 * If a match is found, the route's parameters will be updated
	 * 
	 * @param string $test
	 * @return boolean
	 */
	public function matches($test)
	{
		if (preg_match($this->route, $test, $matches)) {
			$this->params = $this->defaultValues;
			
			foreach ($matches as $i => $value) {
				$value = trim($value);
				
				if (isset($this->matchNames[$i]) && strlen($value) > 0) {
					$name = $this->matchNames[$i];
					$this->params[$name] = $value;
				}
			}
			
			return true;
		}
		
		return false;
	}
	
	/**
	 * Get the route's parameters
	 * 
	 * @return array
	 */
	public function params()
	{
		return $this->params;
	}
	
	/**
	 * Get the name of the module found by the route
	 * 
	 * @return string
	 */
	public function module()
	{
		return isset($this->params[self::PARAM_MODULE])
			? $this->params[self::PARAM_MODULE]
			: null;
	}
	
	/**
	 * Get the name of the controller found by the route
	 * 
	 * @return string
	 */
	public function controller()
	{
		return isset($this->params[self::PARAM_CONTROLLER])
			? $this->params[self::PARAM_CONTROLLER]
			: null;
	}
	
	/**
	 * Get the name of the action found by the route
	 * 
	 * @return string
	 */
	public function action()
	{
		return isset($this->params[self::PARAM_ACTION])
			? $this->params[self::PARAM_ACTION]
			: null;
	}
	
	public function isActive() { return $this->isActive; }
	public function enable() { $this->isActive = true; }
	public function disable() { $this->isActive = false; }
}