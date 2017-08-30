<?php
namespace Database\Query;

class QueryParams
{
	/**
	 * @var array
	 */
	public $conditions = [];
	
	/**
	 * @var array
	 */
	public $orderings = [];
	
	/**
	 * @var int
	 */
	public $maxResults = 0;
	
	/**
	 * @var int
	 */
	public $resultOffset = 0;
	
	/**
	 * Constructor
	 * 
	 * @param array $conditions
	 * @param array $orderings
	 * @param int $maxResults
	 * @param int $resultOffset
	 */
	public function __construct(
		array $conditions = [], 
		array $orderings = [], 
		int $maxResults = 0, 
		int $resultOffset = 0
	) {
		$this->conditions = $conditions;
		$this->orderings = $orderings;
		$this->maxResults = $maxResults;
		$this->resultOffset = $resultOffset;
	}
}