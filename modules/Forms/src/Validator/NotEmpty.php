<?php
namespace Forms\Validator;

class NotEmpty implements ValidatorInterface 
{
	private $message;
	
	public function __construct($message = null)
	{
		$this->message = $message;
	}
	
	public function isValid(\Forms\Field $field, \Forms\AbstractForm $form) : bool 
	{
		return !empty($field->value);
	}
	
	public function getErrorMessage() : string 
	{
		return $this->message ?: "Field must not be empty";
	}
}
