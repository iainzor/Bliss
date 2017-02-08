<?php
namespace Core;

use Common\StringOperations;

class ControllerDefinition
{
	/**
	 * @var ModuleDefinition
	 */
	private $module;
	
	/**
	 * @var string
	 */
	private $className;
	
	/**
	 * @var object
	 */
	private $instance;
	
	/**
	 * @var ActionDefinition[]
	 */
	private $actions = [];
	
	/**
	 * @var boolean
	 */
	private $initialized = false;
	
	/**
	 * Constructor
	 * 
	 * @param ModuleDefinition $module
	 * @param string $className
	 */
	public function __construct(ModuleDefinition $module, $className)
	{
		$this->module = $module;
		$this->className = $className;
	}
	
	/**
	 * @return object
	 */
	public function module()
	{
		return $this->module;
	}
	
	/**
	 * @return string
	 */
	public function className() : string
	{
		return $this->className;
	}
	
	/**
	 * Get the controller's instance
	 * 
	 * @param \Core\AbstractApplication $app
	 * @return object
	 * @throws \Exception
	 */
	public function instance(AbstractApplication $app)
	{
		if (!$this->instance) {
			if (!class_exists($this->className)) {
				throw new \Exception("Controller class '{$this->className}' could not be found");
			}
			$this->instance = $app->di()->create($this->className);
		}
		return $this->instance;
	}
	
	/**
	 * Create a new definition for a controller's action
	 * 
	 * @param string $actionName
	 * @return \Core\ActionDefinition
	 */
	public function action($actionName) : ActionDefinition
	{
		$stringOps = new StringOperations();
		$name = $stringOps->camelize($actionName, false);
		
		if (!isset($this->actions[$name])) {
			$this->actions[$name] = new ActionDefinition($this, $name);
		}
		
		return $this->actions[$name];
	}
	
	/**
	 * Initialize the controller if it hasn't already been initialized
	 * 
	 * @param \Core\AbstractApplication $app
	 */
	public function initialize(AbstractApplication $app)
	{
		if (!$this->initialized) {
			$this->module->initialize($app);
			
			// TODO...
			
			$this->initialized = true;
			$app->di()->register($this->instance($app));
		}
	}
}