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
		$properties = $classRef->getProperties();
		$data = [];
		
		foreach ($properties as $property) {
			$value = $property->getValue($this);
			$name = $property->getName();
			
			if (!empty($value) || $value === null) {
				$data[$name] = $value;
			}
		}
		
		return $data;
	}
}