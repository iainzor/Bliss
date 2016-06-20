<?php
namespace Bliss;

require_once "ArrayExportTrait.php";

class Component
{
	use ArrayExportTrait {
		ArrayExportTrait::toArray as private baseToArray;
		ArrayExportTrait::toBasicArray as private baseToBasicArray;
	}
	
	const VALUE_INT = "intval";
	const VALUE_FLOAT = "floatval";
	const VALUE_DOUBLE = "doubleval";
	const VALUE_STRING = "strval";
	const VALUE_BOOLEAN = "boolval";
	
	private $_properties = [];
	
	/**
	 * Convert the component's properties to an array
	 *
	 * @return array
	 */
	public function toArray()
	{
		return $this->_addCustomProperties(
			$this->baseToArray()
		);
	}
	
	/**
	 * Returns only the properties that aren't instances of Component
	 * 
	 * @return array
	 */
	public function toBasicArray()
	{
		return $this->_addCustomProperties(
			$this->baseToBasicArray()
		);
	}
	
	/**
	 * Add custom properties to the exported array
	 * 
	 * @param array $data
	 * @return array
	 */
	private function _addCustomProperties(array $data)
	{
		foreach ($this->_properties as $name => $value) {
			if (!isset($data[$name])) {
				$data[$name] = $this->_parse(null, $value);
			}
		}
		return $data;
	}
	
	/**
	 * Get or set a property of the component
	 * If the value is NULL the method acts as a getter and returns the current value
	 * If the value is NOT NULL the method first sets the property and then returns the new value
	 * 
	 * Only properties that are part of the class or have been explicitly set in $this._properties are affected
	 * 
	 * @param string $property
	 * @param mixed $value
	 * @param callable $valueParser OPTIONAL Function used to parse the value
	 */
	public function getSet($property, $value = null, $valueParser = null)
	{
		$exists = $property != "_properties" ? property_exists($this, $property) : false;
		
		if ($value !== null && is_callable($valueParser)) {
			$value = call_user_func($valueParser, $value);
		}
		
		if ($exists && $value !== null) {
			$ref = new \ReflectionProperty($this, $property);
			if (!$ref->isPrivate()) {
				$this->{$property} = $value;
			}
		} elseif ($value !== null) {
			$this->_properties[$property] = $value;
		} elseif (isset($this->_properties[$property])) {
			return $this->_properties[$property];
		} elseif (isset($this->{$property})) {
			return $this->{$property};
		}
		
		return null;
	}
	
	/**
	 * Set a custom property value
	 *   
	 * @param string $property
	 * @param mixed $value
	 */
	public function set($property, $value = null)
	{
		if (method_exists($this, $property)) {
			call_user_func([$this, $property], $value);
		} else {
			$this->_properties[$property] = $value;
		}
	}
	
	/**
	 * Magic getter and setter for additional properties
	 * 
	 * @param string $name
	 * @param array $args
	 * @return mixed
	 * @throws \Exception
	 */
	public function __call($name, array $args = [])
	{
		$value = isset($args[0]) ? $args[0] : null;
		$ref = new \ReflectionClass($this);
		
		if (!$ref->hasProperty($name)) {
			return $this->getSet($name, $value);
		} else {
			$prop = $ref->getProperty($name);
			if (!$prop->isPrivate()) {
				throw new \Exception("Unknown method: ". get_class($this) ."::". $name);
			}
		}
	}
	
	/**
	 * Generate a new instance of the calling class using late static binding
	 * 
	 * @param array $properties
	 * @return static
	 */
	final public static function factory(array $properties)
	{
		$instance = new static();
		
		return self::populate($instance, $properties);
	}
	
	/**
	 * Populate a component with a set of properties
	 * 
	 * @param \Bliss\Component $component
	 * @param array|Component $properties
	 * @return \Bliss\Component
	 */
	final public static function populate(Component $component, $properties)
	{
		$properties = self::convertValueToArray($properties);
		
		foreach ($properties as $name => $value) {
			call_user_func([$component, $name], $value);
		}
		
		return $component;
	}
	
	/**
	 * Convert a mixed value to an array
	 * 
	 * @param mixed $value
	 * @return array
	 * @throws \UnexpectedValueException
	 */
	final public static function convertValueToArray($value)
	{
		if ($value instanceof \Bliss\Component) {
			$parsed = $value->toArray();
		} else if ($value instanceof \stdClass) {
			$parsed = (array) $value;
		} else {
			$parsed = $value;
		}
		
		if (!is_array($parsed)) {
			$type = gettype($parsed) === "object" ? get_class($parsed) : gettype($parsed);
			
			throw new \UnexpectedValueException("\$value must be an array or an instance of \\Bliss\\Component, `{$type}` given");
		}
		
		return $parsed;
	}
}
