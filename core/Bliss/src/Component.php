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
			
			if (method_exists($this, $name)) {
				$value = call_user_func([$this, $name]);
			} else {
				$value = $this->{$name};
			}
			
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
	 * Parse a value based on its type
	 * 
	 * @param string $name
	 * @param mixed $value
	 * @return mixed
	 */
	protected function _parse($name, $value)
	{
		$newValue = null;
		
		if (is_array($newValue)) {
			foreach ($newValue as $n => $v) {
				$newValue[$n] = $this->_parse($n, $v);
			}
		} else if (is_object($value) && method_exists($value, "toArray") ) {
			$newValue = $value->toArray();
		} else if ($value instanceOf \DateTime) {
			$newValue = $value->getTimestamp();
		} else if (method_exists($this, $name)) {
			$newValue = call_user_func(array($this, $name));
		} else {
			$newValue = $value;
		}
		
		return $newValue;
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
		
		if (!property_exists($this, $name)) {
			return $this->getSet($name, $value);
		}
		
		throw new \Exception("Unknown method: ". get_class($this) ."::". $name);
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
			if (method_exists($component, $name)) {
				call_user_func([$component, $name], $value);
			} else {
				$component->getSet($name, $value);
			}
		}
		
		return $component;
	}
}
