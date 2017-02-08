<?php
namespace Core;

use Common\StringOperations;

class ModuleDefinition
{
	/**
	 * @var string
	 */
	private $namespace;
	
	/**
	 * @var string
	 */
	private $rootDir;
	
	/**
	 *
	 * @var AbstractModule
	 */
	private $instance;
	
	/**
	 * @var ControllerRegistry[]
	 */
	private $controllers = [];
	
	/**
	 * @var boolean
	 */
	private $initialized = false;
	
	/**
	 * Constructor
	 * 
	 * @param string $namespace
	 * @param string $rootDir
	 */
	public function __construct(string $namespace, string $rootDir)
	{
		$this->namespace = $namespace;
		$this->rootDir = $rootDir;
	}
	
	/**
	 * Get the module's instance
	 * 
	 * @param AbstractApplication $app
	 * @return mixed
	 * @throws \Exception
	 */
	public function instance(AbstractApplication $app)
	{
		if (!$this->instance) {
			$className = $this->namespace ."\\Module";
			
			if (!class_exists($className)) {
				throw new \Exception("This module is registered as a library only.");
			}
			
			$this->instance = $app->di()->create($className);
		}
		return $this->instance;
	}
	
	/**
	 * Get the definition for a controller within the module
	 * 
	 * @param string $controllerName
	 * @return ControllerDefinition
	 */
	public function controller($controllerName) : ControllerDefinition
	{
		$stringOps = new StringOperations();
		$name = $stringOps->camelize($controllerName);
		
		if (!isset($this->controllers[$name])) {
			$className = $this->namespace ."\\Controller\\". $name;
			$this->controllers[$name] = new ControllerDefinition($this, $className);
		}
		
		return $this->controllers[$name];
	}
	
	/**
	 * Initialize the module if it hasn't been already
	 * 
	 * @param \Core\AbstractApplication $app
	 */
	public function initialize(AbstractApplication $app)
	{
		if (!$this->initialized) {
			// TODO...
			$this->initialized = true;
			$app->di()->register($this->instance($app));
		}
	}
}