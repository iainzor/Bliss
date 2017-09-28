<?php
namespace Forms\Validator;

class Callback implements ValidatorInterface 
{
	private $callback;
	
	public function __construct(callable $callback)
	{
		$this->callback = $callback;
	}
	
	public function validate(\Forms\Field $field, \Forms\AbstractForm $form): bool 
	{
		return call_user_func_array($this->callback, [$field, $form]);
	}
}
