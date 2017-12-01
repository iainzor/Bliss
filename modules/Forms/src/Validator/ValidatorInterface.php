<?php
namespace Forms\Validator;

use Forms\AbstractForm,
	Forms\Field;

interface ValidatorInterface
{
	public function isValid(Field $field, AbstractForm $form) : bool;
	
	public function getErrorMessage() : string;
}