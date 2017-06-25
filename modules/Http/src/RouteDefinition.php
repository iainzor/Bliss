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
	private $module = "";
	
	/**
	 * @var mixed
	 */
	private $controller = "";
	
	/**
	 * @var mixed
	 */
	private $action = "";
	
	/**
	 * @var mixed
	 */
	private $params = [];
	
	/**
	 * @var int
	 */
	private $weight = -1;
	
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
	 * Set the weight of the route.  The heavier the weight, the more likely it will be
	 * selected
	 * 
	 * @param int $weight
	 * @return \self
	 */
	public function weight(int $weight) : self
	{
		$this->weight = $weight;
		
		return $this;
	}
	
	/**
	 * @return int
	 */
	public function getWeight() : int
	{
		return $this->weight;
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
		
		$moduleName = $this->_get("module", $matches);
		$controllerName = $this->_get("controller", $matches);
		$actionName = $this->_get("action", $matches);
		$params = is_callable($this->params) ? call_user_func($this->params, $matches) : $this->_generateParams($matches, $this->params);
		
		if (isset($params["module"])) {
			$moduleName = $params["module"];
			unset($params["module"]);
		}
		
		if (isset($params["controller"])) {
			$controllerName = $params["controller"];
			unset($params["controller"]);
		}
		
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
	 * @param string $partName
	 * @param array $matches
	 * @return string
	 * @throws \UnexpectedValueException
	 */
	private function _get(string $partName, array $matches) : string
	{
		$current = $this->{$partName};
		
		if ($current instanceof \Closure) {
			$current = call_user_func($current, $matches);
		}
		
		if (!is_string($current)) {
			throw new \UnexpectedValueException("Route part '{$partName}' must be a closure or a string");
		}
		
		return $current;
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