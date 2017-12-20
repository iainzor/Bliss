<?php
namespace Database\Model;

use Database\Table\WritableTableInterface;

abstract class AbstractModel implements \JsonSerializable, TableLinkedModelInterface
{
	use \Common\ToArrayTrait,
		\Common\PopulatePropertiesTrait;
	
	/**
	 * @var static
	 */
	protected $_cleanModel;
	
	/**
	 * @var \Database\Table\WritableTableInterface
	 */
	protected $_table; 
	
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
		$this->populateProperties($properties, $map);
		$this->_cleanModel = clone $this;
	}
	
	/**
	 * Set the table where this model was retrieved from.
	 * 
	 * @param \Database\Table\WritableTableInterface $table
	 */
	public function setTable(WritableTableInterface $table) 
	{
		$this->_table = $table;
	}
	
	/**
	 * Save any changes made to the model
	 * 
	 * @return bool
	 */
	public function save() : bool
	{
		$classRef = new \ReflectionClass($this);
		$properties = $classRef->getProperties(\ReflectionProperty::IS_PUBLIC);
		$toUpdate = [];
		$primaryKeys = $this->_table->getPrimaryKeys();
		$params = [];
		
		if (empty($primaryKeys)) {
			throw new \Exception("Cannot update model record without one or more primary keys");
		}
		
		foreach ($primaryKeys as $key) {
			if (!isset($this->{$key}) || empty($this->{$key})) {
				throw new \Exception("Could not find a value for primary key '{$key}'");
			}
			
			$params[$key] = $this->{$key};
		}
		
		foreach ($properties as $property) {
			$name = $property->getName();
			$cleanValue = $this->_cleanModel->{$name};
			$currentValue = $this->{$name};
			
			if ($cleanValue !== $currentValue) {
				$toUpdate[$name] = $currentValue;
			}
		}
		
		if (!empty($toUpdate)) {
			$this->_table->update($toUpdate, $params);
			return true;
		}
		return false;
	}
	
	/**
	 * Convert the model to a JSON encodable array
	 * 
	 * @return array
	 */
	public function jsonSerialize() : array
	{
		$data = $this->toArray();
		
		if ($this instanceof ProtectedDataInterface) {
			foreach ($this->getProtectedFields() as $field) {
				unset($data[$field]);
			}
		}
		
		if ($this instanceof AdditionalDataInterface) {
			$data = array_merge($data, $this->getAdditionalData());
		}
		
		return $data;
	}
}
