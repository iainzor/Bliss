<?php
namespace Forms\Validator;

class NotEmpty implements ValidatorInterface 
{
	private $message;
	
	public function __construct($message = null)
	{
		$this->message = $message;
	}
	
	public function validate(\Forms\Field $field, \Forms\AbstractForm $form): bool 
	{
		if (empty($field->value)) {
			$field->setError(
				$this->message ?: "Field must not be empty"
			);
			return false;
		}
		return true;
	}
}
