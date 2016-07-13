<?php
namespace Acl;

class Action
{
	/**
	 * @var string
	 */
	private $name;
	
	/**
	 * @var boolean
	 */
	private $isAllowed = true;
	
	/**
	 * Constructor
	 * 
	 * @param string $name
	 * @param boolean $isAllowed
	 */
	public function __construct($name, $isAllowed = true)
	{
		$this->name($name);
		$this->isAllowed($isAllowed);
	}
	
	/**
	 * Get or set the name of the action
	 * 
	 * @param string $name
	 * @return string
	 */
	public function name($name = null)
	{
		if ($name !== null) {
			$this->name = $name;
		}
		return $this->name;
	}
	
	/**
	 * Get or set whether the action is allowed
	 * 
	 * @param boolean $isAllowed
	 * @return boolean
	 */
	public function isAllowed($isAllowed = null)
	{
		if ($isAllowed !== null) {
			$this->isAllowed = (boolean) $isAllowed;
		}
		return $this->isAllowed;
	}
}