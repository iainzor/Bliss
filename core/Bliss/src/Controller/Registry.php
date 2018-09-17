<?php
namespace Bliss\Controller;

use Bliss\String,
	Bliss\Module\ModuleInterface;

class Registry
{
	/**
	 * @var \Bliss\Module\ModuleInterface
	 */
	private $module;
	
	/**
	 * @var \Bliss\Controller\ControllerInterface[]
	 */
	private $controllers = [];
	
	/**
	 * Constructor
	 * 
	 * @param \Bliss\Module\ModuleInterface $module
	 */
	public function __construct(ModuleInterface $module)
	{
		$this->module = $module;
	}
	
	/**
	 * Attempt to get a controller instance
	 * 
	 * @param string $controllerName
	 * @return \Bliss\Controller\ControllerInterface
	 * @throws \Exception
	 */
	public function get($controllerName)
	{
		if (!isset($this->controllers[$controllerName])) {
			$namespace = preg_replace("/^(.*)\\Module$/i", "\\1", get_class($this->module));
			$className = $namespace ."Controller\\". String::toCamelCase($controllerName) ."Controller";
			
			if (!class_exists($className)) {
				throw new \Exception("Invalid controller: {$controllerName}", 404);
			}
			
			$controller = new $className($this->module);
			
			if (!($controller instanceof ControllerInterface)) {
				throw new \Exception("Controller '{$className}' must be an instance of Bliss\\Controller\\ControllerInterface");
			}
			
			$controller->init();
			
			$this->controllers[$controllerName] = $controller;
		}
		
		return $this->controllers[$controllerName];
	}
}