<?php
namespace Database\Column;

class NamedColumn implements ColumnInterface
{
	/**
	 * @var string
	 */
	private $name;
	
	/**
	 * Constructor
	 * 
	 * @param string $name
	 */
	public function __construct(string $name) 
	{
		$this->name = $name;
	}
	
	/**
	 * @return string
	 */
	public function getValue() : string
	{
		return $this->name;
	}
}
