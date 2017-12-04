<?php
namespace Core;

class DI
{
	/**
	 * @var array
	 */
	private $instances = [];
	
	/**
	 * The injector needs to register itself
	 */
	public function __construct() 
	{
		$this->register($this);
	}
	
	/**
	 * Register a class instance
	 * 
	 * @param object|string $class
	 * @param callable|object $callable
	 * @throws \UnexpectedValueException
	 */
	public function register($class, $callable = null)
	{
		if ($callable !== null) {
			if (!is_string($class)) {
				throw new \UnexpectedValueException("If providing a callback function, \$class must be a string");
			}
			if (is_callable($callable) || is_object($callable)) {
				$this->instances[$class] = $callable;
			} else {
				throw new \InvalidArgumentException("\$callable parameter must be a callback function or an already instantiated class");
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
	 * Get an instance of a class registered to the injector.  If an instance
	 * cannot be found, there will be an attempt to create it.  Throws an exception
	 * if there is an issue instantiating the class instance.
	 * 
	 * If $store is set to TRUE, the created instance will be stored in the injector
	 * for later consumption.
	 * 
	 * @param string $class The name of the class to get
	 * @param array $injectables Optional injectable parameters if the class needs to be created
	 * @param boolean $store Whether to store the instance that is created
	 * @return object
	 * @throws \Exception
	 */
	public function get(string $class, array $injectables = [], bool $store = false)
	{
		$instance = isset($this->instances[$class])
			? $this->instances[$class]
			: $this->create($class, $injectables);
		
		if (is_callable($instance)) {
			$instance = $this->call($instance, $injectables);
		}
		
		if (!isset($this->instances[$class]) && $store === true) {
			$this->instances[$class] = $instance;
		}
		
		return $instance;
	}
	
	/**
	 * Create a new class instance with all of its dependencies injected
	 * 
	 * @param string $class
	 * @param array $injectables
	 * @return object
	 */
	public function create(string $class, array $injectables = [])
	{
		$ref = new \ReflectionClass($class);
		if (!$ref->isInstantiable()) {
			throw new \InvalidArgumentException("\$class cannot be instantiated");
		}
		$const = $ref->getConstructor();
		$params = $const ? $this->_generateParams($const->getParameters(), $injectables) : [];

		return call_user_func_array([$ref, "newInstance"], $params);
	}
	
	/**
	 * Execute a callable with all its dependencies injected
	 * 
	 * @param \Core\callable $callable
	 * @param array $injectables
	 * @return mixed
	 */
	public function call(callable $callable, array $injectables = [])
	{
		if (is_array($callable) && count($callable) > 1) {
			return $this->_callMethod($callable[0], $callable[1], $injectables);
		} else {
			return $this->_callFunction($callable, $injectables);
		}
	}
	
	/**
	 * Private method used to inject into and call class methods
	 * 
	 * @param object $object
	 * @param string $method
	 * @param array $injectables
	 * @return mixed
	 */
	private function _callMethod($object, string $method, array $injectables)
	{
		$ref = new \ReflectionMethod($object, $method);
		$params = $this->_generateParams($ref->getParameters(), $injectables);
		
		return call_user_func_array([$object, $method], $params);
	}
	
	/**
	 * Private method used to inject into and call functions or closures
	 * 
	 * @param callable $func
	 * @param array $injectables
	 * @return mixed
	 */
	private function _callFunction(callable $func, array $injectables)
	{
		$ref = new \ReflectionFunction($func);
		$params = $this->_generateParams($ref->getParameters(), $injectables);
		
		return call_user_func_array($func, $params);
	}
	
	/**
	 * Generate a list of parameters used when calling functions
	 * 
	 * @param \ReflectionParameter[] $parameters
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
				$className = $class->getName();
				$classRef = new \ReflectionClass($className);

				if (isset($injectables[$className])) {
					$value = $injectables[$className];
				} else if (isset($this->instances[$className])) {
					$value = $this->instances[$className];
					if (is_callable($value)) {
						$value = $this->instances[$className] = $this->call($value, $injectables);
					}
				} else if (!$classRef->isInstantiable()) {
					throw new \Exception("{$className} cannot be instantiated and must be provided to the injector");
				} else {
					$value = $this->create($className, $injectables);
				}
			}

			$params[] = $value;
		}
		
		return $params;
	}
}