<?php
namespace Bliss;

class Component
{
	/**
	 * Convert the component's properties to an array
	 *
	 * @return array
	 */
	public function toArray()
	{
		$refClass = new \ReflectionClass($this);
		$props = $refClass->getProperties(\ReflectionProperty::IS_PROTECTED | \ReflectionProperty::IS_PUBLIC);
		$data = [];

		foreach ($props as $refProp) {
			$name = $refProp->getName();
			$value = $this->{$name};
			
			$data[$name] = $this->_parse($name, $value);
		}
		
		/*
		foreach ($refClass->getConstants() as $name => $value) {
			$data["_constants"][$name] = $value;
		}
		*/
		
		return $data;
	}
	
	/**
	 * Returns only the properties that aren't instances of Component
	 * 
	 * @return array
	 */
	public function toBasicArray()
	{
		$refClass = new \ReflectionClass($this);
		$props = $refClass->getProperties(\ReflectionProperty::IS_PROTECTED | \ReflectionProperty::IS_PUBLIC);
		$data = [];

		foreach ($props as $refProp) {
			$name = $refProp->getName();
			$value = $this->{$name};
			
			if ($value instanceof Component) {
				continue;
			}
			
			$data[$name] = $this->_parse($name, $value);
		}
		
		return $data;
	}
	
	/**
	 * Parse a value based on its type
	 * 
	 * @param string $name
	 * @param mixed $value
	 * @return mixed
	 */
	protected function _parse($name, $value)
	{
		$newValue = null;
		
		if (is_object($value) && method_exists($value, "toArray") ) {
			$newValue = $value->toArray();
		} else if ($value instanceOf \DateTime) {
			$newValue = $value->getTimestamp();
		} else if (method_exists($this, $name)) {
			$newValue = call_user_func(array($this, $name));
		} else {
			$newValue = $value;
		}
		
		if (is_array($newValue)) {
			$parsed = [];
			foreach ($newValue as $n => $v) {
				$parsed[$n] = $this->_parse($n, $v);
			}
		}
		
		return $newValue;
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
	 * @param array $properties
	 * @return \Bliss\Component
	 */
	final public static function populate(Component $component, array $properties)
	{
		foreach ($properties as $name => $value) {
			call_user_func([$component, $name], $value);
		}
		
		return $component;
	}
}