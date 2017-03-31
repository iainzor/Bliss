<?php
namespace Database\Model;

use Common\StringOperations;

abstract class AbstractModel implements \JsonSerializable
{
	/**
	 * @var static
	 */
	private $_cleanModel;
	
	/**
	 * Constructor
	 * Provide a clean set of properties for a new model.
	 * An optional map can be provided that will convert the keys of all found properties
	 * 
	 * @param array $properties [propertyName => propertyValue] 
	 * @param array $map [propertyName => newPropertyName]
	 */
	public function __construct(array $properties = [], array $map = [])
	{
		foreach ($properties as $name => $value) {
			if (isset($map[$name])) {
				$name = $map[$name];
			}
			
			if (property_exists($this, $name)) {
				$this->{$name} = $value;
			}
		}
		
		$this->_cleanModel = clone $this;
	}
	
	public function jsonSerialize()
	{
		$strOps = new StringOperations();
		$ref = new \ReflectionClass($this);
		$data = [];
		
		foreach ($ref->getProperties(\ReflectionProperty::IS_PUBLIC) as $property) {
			$name = $property->getName();
			$value = $this->{$name};
			
			if (is_string($value)) {
				$value = $strOps->convertValueType($value);
			}
			$data[$name] = $value;
		}
		
		return $data;
	}
}