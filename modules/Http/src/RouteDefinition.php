<?php
namespace Http;

class RouteDefinition
{
	/**
	 * @var string
	 */
	private $test;
	
	/**
	 * @var mixed
	 */
	private $module;
	
	/**
	 * @var mixed
	 */
	private $controller;
	
	/**
	 * @var mixed
	 */
	private $action;
	
	/**
	 * @var mixed
	 */
	private $params = [];
	
	/**
	 * Constructor
	 * 
	 * @param string $test A regex string used to match paths
	 */
	public function __construct(string $test)
	{
		$this->test = $test;
	}
	
	/**
	 * Set the name of the module to execute for this route.  If a callback
	 * function is provided, it will be called with the matches from the route test.
	 * 
	 * @param string|callable $moduleName
	 * @return self
	 */
	public function module($moduleName) : self
	{
		$this->module = $moduleName;
		
		return $this;
	}
	
	/**
	 * Set the name of the controller to execute for this route.  If a callback
	 * function is provided, it will be called with the matches from the route test.
	 * 
	 * @param string|callable $controllerName
	 * @return self
	 */
	public function controller($controllerName) : self
	{
		$this->controller = $controllerName;
		
		return $this;
	}
	
	/**
	 * Set the name of the action to execute for this route.  If a callback
	 * function is provided, it will be called with the matches from the route test.
	 * 
	 * @param string|callable $actionName
	 * @return self
	 */
	public function action($actionName) : self
	{
		$this->action = $actionName;
		
		return $this;
	}
	
	/**
	 * 
	 * @param array|callable $params
	 * @return self
	 */
	public function params($params) : self
	{
		$this->params = $params;
		
		return $this;
	}
	
	/**
	 * Check if the route matches the provided path
	 * 
	 * @param string $path
	 * @return bool
	 */
	public function	isMatch(string $path) : bool
	{
		if (preg_match($this->test, $path, $matches)) {
			return true;
		}
		return false;
	}
	
	/**
	 * Generate a route instance using the provided path
	 * 
	 * @param string $path
	 * @return \Http\Route
	 */
	public function generateRoute(string $path) : Route 
	{
		preg_match($this->test, $path, $matches);
		
		$moduleName = is_callable($this->module) ? call_user_func($this->module, $matches) : $this->module;
		$controllerName = is_callable($this->controller) ? call_user_func($this->controller, $matches) : $this->controller;
		$actionName = is_callable($this->action) ? call_user_func($this->action, $matches) : $this->action;
		$params = is_callable($this->params) ? call_user_func($this->params, $matches) : $this->_generateParams($matches, $this->params);
		
		if (isset($params["action"])) {
			$actionName = $params["action"];
			unset($params["action"]);
		}
		
		return new Route(
			$moduleName,
			$controllerName,
			$actionName,
			$params
		);
	}
	
	/**
	 * Generate a list of parameters that were parsed from the route
	 * 
	 * @param array $matches
	 * @param array $params
	 * @return array
	 */
	private function _generateParams(array $matches, array $params) : array
	{
		$found = [];
		foreach ($params as $key => $value) {
			if (is_numeric($key) && !empty($matches[$key])) {
				$found[$value] = $matches[$key];
			} else if (!is_numeric($key)) {
				$found[$key] = $value;
			}
		}
		return $found;
	}
}