<?php
namespace Bliss;

trait GetSetTrait
{
	/**
	 * Get or set a property of the instance.
	 * If the value is NULL the method acts as a getter and returns the current value.
	 * If the value is NOT NULL the method first sets the property and then returns the new value.
	 * 
	 * Only properties that are part of the class which are either public or protected are affected
	 * 
	 * @param string $property
	 * @param mixed $value
	 * @param callable $valueParser OPTIONAL Function used to parse the value
	 */
	protected function getSet($property, $value = null, $valueParser = null)
	{
		$ref = new \ReflectionClass($this);
		if (!$ref->hasProperty($property)) {
			throw new \InvalidArgumentException("Invalid property: ". get_class($this) ."::\$". $property);
		}
		
		if ($value !== null) {
			$refProp = new \ReflectionProperty($this, $property);
			
			if ($valueParser !== null && is_callable($valueParser)) {
				$value = call_user_func($valueParser, $value);
			}
			
			if ($refProp->isPrivate()) {
				throw new \Exception("Cannot use getSet to set private properties");
			} else {
				$this->{$property} = $value;
			}
		}
		
		return $this->{$property};
	}
}