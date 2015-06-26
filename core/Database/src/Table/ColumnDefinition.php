<?php
namespace Database\Table;

use Bliss\Component;

class ColumnDefinition extends Component 
{
	/**
	 * @var string
	 */
	protected $name;
	
	/**
	 * @var string
	 */
	protected $displayName;
	
	/**
	 * @var string
	 */
	protected $type;
	
	/**
	 * @var boolean
	 */
	protected $isVisible = true;
	
	/**
	 * Constructor
	 * 
	 * @param string $name
	 * @param array $definition
	 */
	public function __construct($name, array $definition = [])
	{
		$this->name($name);
		
		self::populate($this, $definition);
	}
	
	/**
	 * Get or set the column's name
	 * 
	 * @param string $name
	 * @return string
	 */
	public function name($name = null)
	{
		return $this->getSet("name", $name);
	}
	
	/**
	 * Get or set the display name of the column
	 * 
	 * @param string $displayName
	 * @return string
	 */
	public function displayName($displayName = null)
	{
		if (($displayName = $this->getSet("displayName", $displayName)) === null) {
			$displayName = $this->name();
		}
		return $displayName;
	}
	
	/**
	 * Get or set the column's type
	 * 
	 * @param string $type
	 * @return string
	 */
	public function type($type = null)
	{
		return $this->getSet("type", $type);
	}
	
	/**
	 * Get or set whether the column is visible
	 * 
	 * @param boolean $flag
	 * @return boolean
	 */
	public function isVisible($flag = null)
	{
		if ($flag !== null) {
			$this->isVisible = (boolean) $flag;
		}
		return $this->isVisible;
	}
	
	/**
	 * Parse a value based on the column's type
	 * 
	 * @param mixed $value
	 * @return mixed
	 */
	public function parseValue($value)
	{
		$type = $this->type();
		
		if (preg_match("/^int\(?[0-9]+?\)?$/i", $type)) {
			return (int) $value;
		} else if (preg_match("/^float\([0-9]+?,?[0-9]+?\)$/i", $type)) {
			return (float) $value;
		} else if (preg_match("/^double\([0-9]+?,?[0-9]+?\)$/i", $type)) {
			return (double) $value;
		}
		
		return $value;
	}
}