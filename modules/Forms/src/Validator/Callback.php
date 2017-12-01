<?php
namespace Forms\Validator;

class Callback implements ValidatorInterface 
{
	/**
	 * @var callable
	 */
	private $callback;
	
	/**
	 * Constructor
	 * 
	 * @param callable $callback
	 * @param string $message
	 */
	public function __construct(callable $callback, string $message = null)
	{
		$this->callback = $callback;
		$this->message = $message;
	}
	
	public function isValid(\Forms\Field $field, \Forms\AbstractForm $form): bool 
	{
		return call_user_func_array($this->callback, [$field, $form]);
	}
	
	public function getErrorMessage() : string 
	{ 
		return $this->message ?: "Field is invalid";
	}
}
