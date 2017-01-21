<?php
namespace Forms\Input;

use Bliss\Component;

abstract class AbstractInput extends Component
{
	protected $type;
	
	/**
	 * Get or set the default value of the input
	 * 
	 * @param mixed $defaultValue
	 * @return mixed
	 */
	public function defaultValue($defaultValue = null)
	{
		return $this->getSet("defaultValue", $defaultValue);
	}
	
	abstract public function type();
}