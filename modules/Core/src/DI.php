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
	
	public function create($class, array $injectables = [])
	{
		$ref = new \ReflectionClass($class);
		$const = $ref->getConstructor();
		$params = [];
		
		if ($const) {
			foreach ($const->getParameters() as $param) {
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
		}
		
		return call_user_func_array([$ref, "newInstance"], $params);
	}
}