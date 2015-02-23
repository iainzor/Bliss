<?php
namespace View\Partial;

class StaticPartial implements PartialInterface
{
	/**
	 * @var string
	 */
	private $contents;
	
	/**
	 * Constructor
	 * 
	 * @param string $contents
	 */
	public function __construct($contents)
	{
		$this->contents = $contents;
	}
	
	/**
	 * Return the static contents of the partial
	 * 
	 * @param array $params
	 * @return string
	 */
	public function render(array $params = array()) 
	{
		return $this->contents;
	}
}