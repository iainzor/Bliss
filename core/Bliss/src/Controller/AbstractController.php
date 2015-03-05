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
		
		$ref = new \ReflectionMethod($this, $methodName);
		$refParams = $ref->getParameters();
		$args = [];
		
		foreach ($refParams as $refParam) {
			$className = $refParam->getClass()->getName();
			if (preg_match("/^([a-z0-9]+).module$/i", $className, $matches)) {
				$args[] = $this->app->module($matches[1]);
			} else {
				throw new \InvalidArgumentException("Could not inject parameter '\${$refParam->getName()}' into '{$methodName}'");
			}
		}
		
		return call_user_func_array([$this, $methodName], $args);
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
		return $this->request()->param($name, $defaultValue);
	}
	
	/**
	 * Forward magic methods to parent module
	 * 
	 * @param string $name
	 * @param array $arguments
	 * @return \Bliss\Module\AbstractModule
	 */
	public function __call($name, array $arguments) 
	{
		return call_user_func_array([$this->module, $name], $arguments);
	}
}