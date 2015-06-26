<?php
namespace Database\Expr;

class GenericExpr implements ExprInterface
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
	public function __construct($expr)
	{
		$this->expr = $expr;
	}
	
	/**
	 * @return string
	 */
	public function toString() 
	{
		return $this->expr;
	}
}