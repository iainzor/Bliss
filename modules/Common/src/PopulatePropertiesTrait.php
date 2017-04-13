<?php
namespace Common;

trait PopulatePropertiesTrait
{
	/**
	 * Attempt to populate all public and protected properties in the class
	 * using an array of [key => value] pairs
	 * 
	 * @param array $properties
	 */
	public function populateProperties(array $properties)
	{
		$classRef = new \ReflectionClass($this);
		foreach ($classRef->getProperties(\ReflectionProperty::IS_PROTECTED | \ReflectionProperty::IS_PUBLIC) as $property) {
			$name = $property->name;
			
			if (isset($properties[$name])) {
				$this->{$name} = $properties[$name];
			}
		}
	}
}