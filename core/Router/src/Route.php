<?php
namespace Router;

use Bliss\Component;

class Route extends Component
{
	const PARAM_MODULE = "module";
	const PARAM_CONTROLLER = "controller";
	const PARAM_ACTION = "action";
	const PARAM_ELEMENT = "element";
	
	/**
	 * @var string
	 */
	protected $route;
	
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
	protected $priority = 1;
	
	/**
	 * @var array
	 */
	private $params = [];
	
	/**
	 * @var boolean
	 */
	protected $isActive = true;
	
	/**
	 * @var int
	 */
	private $timesUsed = 0;
	
	/**
	 * @var string
	 */
	protected $element;
	
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
	 * Get or set the element used by the route
	 * 
	 * @param string $element
	 * @return string
	 */
	public function element($element = null)
	{
		if ($element !== null) {
			$this->element = $element;
		}
		if (!$this->element && isset($this->defaultValues[self::PARAM_ELEMENT])) {
			$this->element = $this->defaultValues[self::PARAM_ELEMENT];
		}
		return $this->element;
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
	
	/**
	 * Increment the number of times the route has been used
	 * 
	 * @param int $amount
	 */
	public function incrementTimesUsed($amount = 1)
	{
		$this->timesUsed += (int) $amount;
	}
	
	/**
	 * Get or set the number of times the route has been used
	 * 
	 * @param int $number
	 * @return int
	 */
	public function timesUsed($number = null)
	{
		if ($number !== null) {
			$this->timesUsed = (int) $number;
		}
		
		return $this->timesUsed;
	}
	
	/**
	 * Override the default toArray method to modify some properties
	 * 
	 * @return string
	 */
	public function toArray() 
	{
		$data = parent::toArray();
		$data["params"] = $this->matchNames;
		
		if (preg_match("/^\/(.*)\/[a-z]*$/", $data["route"], $matches)) {
			$data["route"] = $matches[1];
		}
		
		return $data;
	}
}