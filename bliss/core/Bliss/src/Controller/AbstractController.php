<?php
namespace Bliss\Controller;

use Bliss\Module\ModuleInterface,
	Bliss\String;

abstract class AbstractController implements ControllerInterface
{
	/**
	 * @var \Bliss\Module\ModuleInterface
	 */
	protected $module;
	
	/**
	 * @var \Bliss\App\Container
	 */
	protected $app;
	
	/**
	 * Constructor
	 * 
	 * @param \Bliss\Module\ModuleInterface $module
	 */
	public function __construct(ModuleInterface $module)
	{
		$this->module = $module;
		$this->app = $module->app();
	}
	
	/**
	 * Blank method to make initialization optional
	 */
	public function init()
	{}
	
	/**
	 * Attempt to executed an action and return its response
	 * 
	 * @param \Request\Module $request
	 * @return string|array
	 * @throws \Exception
	 */
	public function execute(\Request\Module $request)
	{
		$actionName = $request->getAction();
		$methodName = String::toCamelCase($actionName) ."Action";
		
		$this->app->log("Executing action: {$actionName}");
		
		if (!method_exists($this, $methodName)) {
			throw new \Exception("Invalid action: {$actionName}");
		}
		
		return call_user_func([$this, $methodName]);
	}
}