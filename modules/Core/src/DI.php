<?php
namespace Core;

class DI
{
	private $instances = [];
	
	/**
	 * Register a class instance
	 * 
	 * @param object|string $class
	 * @param callable $callback
	 * @throws \UnexpectedValueException
	 */
	public function register($class, callable $callback = null)
	{
		if ($callback !== null) {
			if (is_string($class)) {
				$this->instances[$class] = $callback;
			} else {
				throw new \UnexpectedValueException("If providing a callback function, \$class must be a string");
			}
		} else if (!is_object($class)) {
			throw new \UnexpectedValueException("If no callback is provided, \$class must be an instantiated class");
		} else {
			$ref = new \ReflectionClass($class);
			$name = $ref->getName();

			$this->instances[$name] = $class;
		}
	}
	
	/**
	 * Create a new class instance with all of its dependencies injected
	 * 
	 * @param string $class
	 * @param array $injectables
	 * @return object
	 */
	public function create($class, array $injectables = [])
	{
		$ref = new \ReflectionClass($class);
		$const = $ref->getConstructor();
		$params = $const ? $this->_generateParams($const->getParameters(), $injectables) : [];
		
		return call_user_func_array([$ref, "newInstance"], $params);
	}
	
	public function call(callable $callable, array $injectables = [])
	{
		if (is_array($callable) && count($callable) > 1) {
			return $this->_callMethod($callable[0], $callable[1], $injectables);
		} else {
			return $this->_callFunction($callable, $injectables);
		}
	}
	
	private function _callMethod($object, $method, array $injectables)
	{
		$ref = new \ReflectionMethod($object, $method);
		$params = $this->_generateParams($ref->getParameters(), $injectables);
		
		return call_user_func_array([$object, $method], $params);
	}
	
	private function _callFunction(callable $func, array $injectables)
	{
		$ref = new \ReflectionFunction($func);
		$params = $this->_generateParams($ref->getParameters(), $injectables);
		
		return call_user_func_array($func, $params);
	}
	
	/**
	 * Generate a list of parameters used when calling functions
	 * 
	 * @param array $parameters
	 * @param array $injectables
	 * @return array
	 */
	private function _generateParams(array $parameters, array $injectables) : array
	{
		$params = [];
		foreach ($parameters as $param) {
			$class = $param->getClass();

			try {
				$value = $param->getDefaultValue();
			} catch (\ReflectionException $e) {
				$value = null;
			}

			if ($class) {	
				$className = $class->name;

				if (isset($injectables[$className])) {
					$value = $injectables[$className];
				} else if (isset($this->instances[$className])) {
					$value = $this->instances[$className];
					if (is_callable($value)) {
						$value = $this->instances[$className] = call_user_func($value);
					}
				} else {
					$value = $this->create($className, $injectables);
				}
			}

			$params[] = $value;
		}
		
		return $params;
	}
}