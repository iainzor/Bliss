<?php
namespace Database\Column;

class ColumnExpr implements ColumnInterface
{
	/**
	 * @var string
	 */
	private $value;
	
	/**
	 * Constructor
	 * 
	 * @param string $value
	 */
	public function __construct(string $value)
	{
		$this->value = $value;
	}
	
	/**
	 * @return string
	 */
	public function getValue()
	{
		return $this->value;
	}
}
