<?php
namespace Forms\Validator;

use Forms\AbstractForm,
	Forms\Field;

interface ValidatorInterface
{
	public function validate(Field $field, AbstractForm $form) : bool;
}