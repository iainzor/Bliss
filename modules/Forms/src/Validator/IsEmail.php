<?php
namespace Forms\Validator;

class IsEmail implements ValidatorInterface 
{
	public function isValid(\Forms\Field $field, \Forms\AbstractForm $form) : bool 
	{
		return preg_match("/^[^@]+@.+$/i", $field->value);
	}
	
	public function getErrorMessage() : string
	{
		return "Please provide a valid email address";
	}
}
