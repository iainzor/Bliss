<?php
namespace User\Settings;

class GenericDefinition extends Definition 
{
	/**
	 * Constructor
	 * 
	 * @param string $key
	 */
	public function __construct($key)
	{
		$this->key($key);
	}
}