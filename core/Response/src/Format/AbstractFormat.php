<?php
namespace Response\Format;

abstract class AbstractFormat implements FormatInterface
{
	/**
	 * @var boolean
	 */
	protected $requiresView = true;
	
	/**
	 * Set whether the format requires a view to render
	 * 
	 * @param boolean $flag
	 */
	public function setRequiresView($flag = true)
	{
		$this->requiresView = (boolean) $flag;
	}
	
	/**
	 * Check if the format requires a view to the render
	 * 
	 * @return boolean
	 */
	public function requiresView() 
	{
		return $this->requiresView;
	}
}