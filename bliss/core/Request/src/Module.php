<?php
namespace Request;

class Module extends \Bliss\Module\AbstractModule
{
	const PARAM_MODULE = "module";
	const PARAM_CONTROLLER = "controller";
	const PARAM_ACTION = "action";
	const PARAM_FORMAT = "format";
	
	/**
	 * @var array
	 */
	private $_defaultParams = [
		self::PARAM_MODULE => "request",
		self::PARAM_CONTROLLER => "index",
		self::PARAM_ACTION => "index",
		self::PARAM_FORMAT => null
	];
	
	/**
	 * @var array
	 */
	private $params;
	
	/**
	 * Set the request's paramters
	 * 
	 * @param array $params
	 */
	public function setParams(array $params)
	{
		$this->params = array_merge($this->params, $params);
	}
	
	/**
	 * Get all parameters of the request
	 * 
	 * @return array
	 */
	public function params()
	{
		if (!isset($this->params)) {
			$this->params = $this->_defaultParams;
		}
		
		return $this->params;
	}
	
	/**
	 * Get a single parameter value
	 * 
	 * @param string $name
	 * @param mixed $defaultValue
	 * @return mixed
	 */
	public function param($name, $defaultValue = null)
	{
		return isset($this->params[$name])
			? $this->params[$name]
			: $defaultValue;
	}
	
	/**
	 * Set a parameter value
	 * 
	 * @param string $name
	 * @param mixed $value
	 */
	public function set($name, $value)
	{
		$this->params[$name] = $value;
	}
	
	/**
	 * Set the request's parameters to the default values
	 */
	public function reset() { $this->params = $this->_defaultParams; }
	
	/**
	 * Getters and setters
	 */
	public function getModule() { return $this->param(self::PARAM_MODULE); }
	public function setModule($module) { $this->set(self::PARAM_MODULE, $module); }
	
	public function getController() { return $this->param(self::PARAM_CONTROLLER); }
	public function setController($controller) { return $this->set(self::PARAM_CONTROLLER, $controller); }
	
	public function getAction() { return $this->param(self::PARAM_ACTION); }
	public function setAction($action) { $this->set(self::PARAM_ACTION, $action); }
	
	public function getFormat() { return $this->param(self::PARAM_FORMAT); }
	public function setFormat($format) { $this->set(self::PARAM_FORMAT, $format); }
}