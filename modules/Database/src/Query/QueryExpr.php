<?php
namespace Database\Query;

class QueryExpr
{
	/**
	 * @var string
	 */
	private $expr;
	
	/**
	 * Constructor
	 * 
	 * @param string $expr
	 */
	public function __construct(string $expr)
	{
		$this->expr = $expr;
	}
	
	/**
	 * @return string
	 */
	public function toString() : string
	{
		return $this->expr;
	}
}