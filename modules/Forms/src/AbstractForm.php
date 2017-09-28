<?php
namespace Forms;

abstract class AbstractForm implements \JsonSerializable
{
	/**
	 * @var Field[]
	 */
	private $fields = [];
	
	/**
	 * @var array
	 */
	private $data = []; 
	
	/**
	 * @var boolean
	 */
	public $isValid = true;
	
	abstract public function onValidated();
	
	abstract public function getFieldNames() : array;
	
	/**
	 * Execute the form
	 * 
	 * @param array $values
	 * @return \self
	 */
	public function execute(array $values) : self
	{
		$this->setValues($values);
		
		if ($this->isValid()) {
			$this->onValidated();
		}
		
		return $this;
	}
	
	/**
	 * Set the form data
	 * 
	 * @param array $data
	 */
	public function setValues(array $values)
	{
		foreach ($this->getFieldNames() as $name) {
			$field = $this->getField($name);
			$field->value = isset($values[$name]) ? $values[$name] : null;
		}
	}
	
	/**
	 * Get a field's value
	 * 
	 * @param string $fieldName
	 * @return mixed
	 */
	public function getValue(string $fieldName)
	{
		return $this->getField($fieldName)->value;
	}
	
	/**
	 * Get a field by its name
	 * 
	 * @param string $name
	 * @return \Forms\Field
	 */
	public function getField(string $name) : Field 
	{
		if (!isset($this->fields[$name])) {
			$this->fields[$name] = new Field($this, [
				"name" => $name
			]);
		}
		return $this->fields[$name];
	}
	
	/**
	 * Add validators for a field
	 * 
	 * @param string $fieldName
	 * @param array $validators
	 */
	public function validate(string $fieldName, array $validators)
	{
		$field = $this->getField($fieldName);
		foreach ($validators as $validator) {
			$field->addValidator($validator);
		}
	}
	
	/**
	 * Add custom data to the form
	 * 
	 * @param string $key
	 * @param mixed $value
	 */
	public function addData(string $key, $value)
	{
		$this->data[$key] = $value;
	}
	
	/**
	 * @return bool
	 */
	private function isValid() : bool
	{
		$this->isValid = true;
		
		foreach ($this->fields as $field) {
			if (!$field->isValid()) {
				$this->isValid = false;
			}
		}
		
		return $this->isValid;
	}
	
	public function jsonSerialize() : array 
	{
		return [
			"fields" => $this->fields,
			"isValid" => $this->isValid,
			"data" => $this->data
		];
	}
}
