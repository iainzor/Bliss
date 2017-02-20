<?php
namespace Core;

class ActionDefinition
{
	/**
	 * @var ControllerDefinition
	 */
	private $controller;
	
	/**
	 * @var string
	 */
	private $name;
	
	/**
	 * Constructor
	 * 
	 * @param \Core\ControllerDefinition $controller
	 * @param string $name
	 */
	public function __construct(ControllerDefinition $controller, string $name)
	{
		$this->controller = $controller;
		$this->name = $name;
	}
	
	/**
	 * Get the name of the action
	 * 
	 * @return string
	 */
	public function name() : string	
	{
		return $this->name;
	}
	
	/**
	 * Call the action and return its results
	 * 
	 * @param \Core\AbstractApplication $app
	 * @return mixed
	 * @throws \Exception
	 */
	public function call(AbstractApplication $app)
	{
		$this->controller->initialize($app);
		
		$method = $this->name ."Action";
		$classInstance = $this->controller->instance($app);
		
		if (!method_exists($classInstance, $method)) {
			throw new \Exception("Controller '". $this->controller->className() ."' does not have the method '{$method}'");
		}
		
		return $app->di()->call([$classInstance, $method]);
	}
}