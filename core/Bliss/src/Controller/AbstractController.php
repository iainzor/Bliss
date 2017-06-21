<?php
namespace Bliss\Controller;

use Bliss\Module\ModuleInterface,
	Bliss\StringUtil;

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
	 * Used to run any initialization methods - Any method matching init*()
	 */
	public function init()
	{
		$refClass = new \ReflectionClass($this);
		foreach ($refClass->getMethods() as $refMethod) {
			if (preg_match("/^init(.+)$/i", $refMethod->name)) {
				$this->app->call($this, $refMethod->name);
			}
		}
	}
	
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
		$methodName = StringUtil::toCamelCase($actionName) ."Action";
		
		$this->app->log("Executing action: {$actionName}");
		
		if (!method_exists($this, $methodName)) {
			throw new \Exception("Invalid action: {$actionName}");
		}
		
		return $this->app->call($this, $methodName);
	}
	
	/**
	 * Attempt to get a parameter from the Request
	 * 
	 * @param string $name
	 * @param mixed $defaultValue
	 * @return mixed
	 */
	public function param($name, $defaultValue = null)
	{
		return $this->app->request()->param($name, $defaultValue);
	}
}