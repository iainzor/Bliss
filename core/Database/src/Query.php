<?php
namespace Database;

use Bliss\Component;

class Query extends Component
{
	const TYPE_SELECT = 0;
	const TYPE_INSERT = 1;
	const TYPE_UPDATE = 2;
	const TYPE_DELETE = 3;
	
	const PART_SQL_START = 0;
	const PART_SQL_END = 100;
	
	const PART_FROM = 1;
	const PART_JOIN = 2;
	const PART_GROUP_BY = 3;
	const PART_ORDER_BY = 4;
	const PART_LIMIT = 5;
	
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
	 * Constructor
	 * 
	 * @param string $tableName
	 * @param array $params
	 */
	public function __construct($tableName, array $params = [])
	{
		$this->tableName = $tableName;
		$this->params = $params;
	}
	
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
		$name = $this->getSet("tableName", $tableName);
		
		if (preg_match("/^`?([a-z0-9-_]+)`?\.?`?([a-z0-9-_]+)?`?/i", $name, $matches)) {
			$name = "`". $matches[1] ."`";
			
			if (!empty($matches[2])) {
				$name .= ".`". $matches[2] ."`";
			}
			
			return $name;
		}
		
		return $name;
	}
	
	public function params(array $params = null, $replace = false)
	{
		if ($params !== null) {
			if ($replace === true) {
				$this->params = $params;
			} else {
				$this->params = array_merge_recursive($this->params, $params);
			}
		}
		return $this->params;
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
	 * Generate a SELECT SQL statement
	 * 
	 * @return string
	 */
	public function generateSelectSql()
	{
		ksort($this->parts);
		
		return $this->sqlFactory()->generateSelectSQL($this);
	}
}