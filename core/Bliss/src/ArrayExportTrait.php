<?php
namespace Bliss;

trait ArrayExportTrait
{
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
			foreach ($newValue as $n => $v) {
				$newValue[$n] = $this->_parse(null, $v);
			}
		}
		
		return $newValue;
	}
}