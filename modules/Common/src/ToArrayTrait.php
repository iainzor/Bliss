<?php
namespace Common;

trait ToArrayTrait
{
	/**
	 * Attempt to convert the object to an array 
	 * 
	 * @return array
	 */
	public function toArray() : array
	{
		$classRef = new \ReflectionClass($this);
		$properties = $classRef->getProperties(\ReflectionProperty::IS_PUBLIC | \ReflectionProperty::IS_PROTECTED);
		$data = [];
		$stringOps = new StringOperations();
		
		foreach ($properties as $property) {
			$value = $property->getValue($this);
			$name = $property->getName();
			
			if (is_string($value)) {
				$value = $stringOps->convertValueType($value);
			}
			
			$data[$name] = $value;
		}
		
		return $data;
	}
}