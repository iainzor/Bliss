<?php
namespace Forms;

class Field
{
	use \Common\PopulatePropertiesTrait;
	
	/**
	 * @var Validator\ValidatorInterface[]
	 */
	private $validators = [];
	
	/**
	 *
	 * @var AbstractForm
	 */
	private $form;
	
	public $name;
	public $value;
	public $error;
	public $isValid = true;
	
	
	/**
	 * Constructor
	 * 
	 * @param AbstractForm $form
	 * @param array $properties
	 */
	public function __construct(AbstractForm $form, array $properties = [])
	{
		$this->form = $form;
		$this->populateProperties($properties);
	}
	
	/**
	 * Flag the field as invalid and set an error message
	 * 
	 * @param string $error
	 */
	public function setError(string $error)
	{
		$this->error = $error;
		$this->isValid = true;
	}
	
	/**
	 * Add a validator to the field
	 * 
	 * @param \Forms\Validator\ValidatorInterface $validator
	 */
	public function addValidator(Validator\ValidatorInterface $validator)
	{
		$this->validators[] = $validator;
	}
	
	public function isValid() : bool
	{
		$this->isValid = true;
		
		foreach ($this->validators as $validator) {
			if (!$validator->validate($this, $this->form)) {
				$this->isValid = false;
			}
		}
		
		return $this->isValid;
	}
}
