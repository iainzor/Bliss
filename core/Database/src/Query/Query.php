<?php
namespace Database\Query;

use Bliss\Component,
	Database\SQLFactory,
	Database\PDO;

class Query extends Component
{
	const TYPE_SELECT = 0;
	const TYPE_INSERT = 1;
	const TYPE_UPDATE = 2;
	const TYPE_DELETE = 3;
	
	const PART_SQL_START = 0;
	const PART_FROM = 10;
	const PART_JOIN = 20;
	const PART_WHERE = 30;
	const PART_GROUP_BY = 40;
	const PART_ORDER_BY = 50;
	const PART_LIMIT = 60;
	const PART_SQL_END = 1000;
	
	const JOIN_DEFAULT = 0;
	const JOIN_LEFT = 1;
	
	/**
	 * @var \Database\SQLFactoryInterface
	 */
	private static $sqlFactory;
	
	/**
	 * @var string
	 */
	protected $tableName;
	
	/**
	 * @var array
	 */
	protected $params = [];
	
	/**
	 * Heh, private parts
	 * 
	 * @var array
	 */
	private $parts = [];
	
	/**
	 * Add a part to the query
	 * 
	 * @param int $id
	 * @param string|Expr $expr
	 */
	public function addPart($id, $expr)
	{
		if (!isset($this->parts[$id])) {
			$this->parts[$id] = [];
		}
		$this->parts[$id][] = $expr;
	}
	
	/**
	 * Get or set the table's name
	 * Upon return, the table will be propertly quoted
	 * 
	 * @param string $tableName
	 * @return string
	 */
	public function tableName($tableName = null)
	{
		return $this->quoteField(
			$this->getSet("tableName", $tableName)
		);
	}
	
	/**
	 * Add a condition to the WHERE clause
	 * 
	 * @param type $expr
	 * @return Query
	 */
	public function where($expr)
	{
		$this->addPart(self::PART_WHERE, $expr);
		
		return $this;
	}
	
	/**
	 * Add an expression to the ORDER BY clause
	 * 
	 * @param string $expr
	 * @return Query
	 */
	public function orderBy($expr)
	{
		$this->addPart(self::PART_ORDER_BY, $expr);
		
		return $this;
	}
	
	/**
	 * Get or set the SQL Factory instance
	 * 
	 * @param \Database\SQLFactory\SQLFactoryInterface $factory
	 * @return \Database\SQLFactory\SQLFactoryInterface
	 */
	public static function sqlFactory(SQLFactory\SQLFactoryInterface $factory = null)
	{
		if ($factory !== null) {
			self::$sqlFactory = $factory;
		}
		if (!self::$sqlFactory) {
			self::$sqlFactory = new SQLFactory\MySQLFactory();
		}
		return self::$sqlFactory;
	}
	
	/**
	 * Generate a SQL statement
	 * 
	 * @param PDO $db
	 * @return string
	 */
	public function sql(PDO $db)
	{
		ksort($this->parts);
		
		$sections = [];
		foreach ($this->parts as $id => $parts) {
			$sections[] = $this->parseParts($id, $parts);
		}
		
		return implode(" ", $sections);
	}
	
	/**
	 * Quote a field using ` characters
	 * 
	 * @param string $input
	 * @return string
	 */
	protected function quoteField($input)
	{
		if (preg_match_all("/`?([a-z0-9-_]+)`?\.?`?([a-z0-9-_]+)?`?(.*as)?/i", $input, $matches)) {
			$items = [];
			
			for ($i = 0; $i < count($matches[1]); $i++) {
				$part1 = $matches[1][$i];
				$part2 = $matches[2][$i];
				$field = "`{$part1}`";
			
				if (!empty($part2)) {
					$field .= ".`{$part2}`";
				}
				
				$field .= $matches[3][$i];
				$items[] = $field;
			}
			
			$input = implode(" ", $items);
		}
		
		return $input;
	}
	
	private function parseParts($id, array $parts)
	{
		$factory = self::sqlFactory();
		
		switch ($id) {
			case self::PART_WHERE:
				return $factory->generateWhereClause($parts);
			case self::PART_ORDER_BY:
				return $factory->generateOrderClause($parts);
			default:
				return implode(" ", $parts);
		}
	}
}