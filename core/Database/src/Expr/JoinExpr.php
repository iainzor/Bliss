<?php
namespace Database\Expr;

class JoinExpr implements ExprInterface
{
	/**
	 * @var string
	 */
	private $columnA;
	
	/**
	 * @var string
	 */
	private $columnB;
	
	/**
	 * Constructor
	 * 
	 * @param string $columnA
	 * @param string $columnB
	 */
	public function __construct($columnA, $columnB)
	{
		$this->columnA = $columnA;
		$this->columnB = $columnB;
	}
	
	/**
	 * @return string
	 */
	public function toString() 
	{
		return "ON ". $this->columnA ." = ". $this->columnB;
	}
}