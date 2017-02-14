<?php
namespace Database\Column;

class AliasedColumn implements ColumnInterface
{
	/**
	 * @var string
	 */
	private $alias;
	
	/**
	 * @var ColumnInterface
	 */
	private $column;
	
	public function __construct(string $alias, ColumnInterface $column)
	{
		$this->alias = $alias;
		$this->column = $column;
	}
	
	/**
	 * Get the alias of the column
	 * 
	 * @return string
	 */
	public function getAlias() : string
	{
		return $this->alias;
	}
	
	/**
	 * Get the column's original value
	 * 
	 * @return mixed
	 */
	public function getValue() 
	{
		return $this->column->getValue();
	}
}
