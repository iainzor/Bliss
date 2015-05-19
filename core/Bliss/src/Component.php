<?php
namespace Bliss;

class Component
{
	private $_properties = [];
	
	/**
	 * Convert the component's properties to an array
	 *
	 * @return array
	 */
	public function toArray()
	{
		return $this->_toArray();
	}
	
	/**
	 * Returns only the properties that aren't instances of Component
	 * 
	 * @return array
	 */
	public function toBasicArray()
	{
		return $this->_toArray(true);
	}
	
	/**
	 * @param boolean $basic
	 * @return array
	 */
	private function _toArray($basic = false)
	{
		$refClass = new \ReflectionClass($this);
		$props = $refClass->getProperties(\ReflectionProperty::IS_PROTECTED | \ReflectionProperty::IS_PUBLIC);
		$data = [];

		foreach ($props as $refProp) {
			$name = $refProp->getName();
			$value = $this->{$name};
			
			if ($basic === true && $value instanceof Component) {
				continue;
			}
			
			$data[$name] = $this->_parse($name, $value);
		}
		
		$diff = array_diff_assoc($this->_properties, $data);
		
		return array_merge($data, $diff);
	}
	
	/**
	 * Get or set a property of the component
	 * If the value IS NULL is provided the method acts as a getter and returns the current value
	 * If the value IS NOT NULL the method first sets the property and then returns the new value
	 * 
	 * @param string $property
	 * @param mixed $value
	 */
	protected function getSet($property, $value = null)
	{
		$exists = $property != "_properties" ? property_exists($this, $property) : false;
		
		if ($exists && $value !== null) {
			$ref = new \ReflectionProperty($this, $property);
			if (!$ref->isPrivate()) {
				$this->{$property} = $value;
			} else if (method_exists($this, $property)) {
				call_user_func([$this, $property], $value);
			}
		} elseif ($value !== null) {
			$this->_properties[$property] = $value;
		} elseif (isset($this->_properties[$property])) {
			return $this->_properties[$property];
		} elseif (isset($this->{$property})) {
			return $this->{$property};
		} elseif (method_exists($this, $property)) {
			return call_user_func([$this, $property]);
		}
		
		return null;
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
			$component->getSet($name, $value);
		}
		
		return $component;
	}
}