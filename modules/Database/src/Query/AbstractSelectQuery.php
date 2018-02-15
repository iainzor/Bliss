<?php
namespace Database\Query;

use Database\Table\TableInterface;

abstract class AbstractSelectQuery implements QueryInterface
{
	/**
	 * @var \Database\Table\TableInterface
	 */
	private $baseTable;
	
	/**
	 * Constructor
	 * 
	 * @param TableInterface $baseTable
	 */
	public function __construct(TableInterface $baseTable)
	{
		$this->baseTable = $baseTable;
	}
}