<?php
namespace User\Settings;

use Database\Model\AbstractModel;

class Setting extends AbstractModel
{
	const TYPE_STRING = "string";
	const TYPE_JSON = "json";
	
	/**
	 * @var Definition
	 */
	private $definition;
	
	/**
	 * @var int
	 */
	protected $userId;
	
	/**
	 * @var string
	 */
	protected $moduleName;
	
	/**
	 * @var string
	 */
	protected $key;
	
	/**
	 * @var mixed
	 */
	protected $value;
	
	/**
	 * @var string
	 */
	protected $type = self::TYPE_STRING;
	
	/**
	 * Constructor
	 * 
	 * @param Definition $definition
	 */
	public function __construct (Definition $definition = null)
	{
		$this->definition = $definition;
	}
	
	/**
	 * Get or set the user ID the setting belongs to
	 * 
	 * @param int $userId
	 * @return int
	 */
	public function userId($userId = null)
	{
		return $this->getSet("userId", $userId, self::VALUE_INT);
	}
	
	/**
	 * Get or set the module name the setting belongs to
	 * 
	 * @param string $name
	 * @return string
	 */
	public function moduleName($name = null)
	{
		return $this->getSet("moduleName", $name);
	}
	
	/**
	 * Get or set the setting key
	 * 
	 * @param string $key
	 * @return string
	 */
	public function key($key = null)
	{
		return $this->getSet("key", $key);
	}
	
	/**
	 * Get or set the value of the setting
	 * 
	 * @param mixed $value
	 * @return mixed
	 */
	public function value($value = null, $encoded = false)
	{
		if ($value !== null) {
			if ($encoded === true && $this->definition) {
				$value = $this->definition->decode($value);
			}
			$this->value = $value;
		}
		
		return $this->value;
	}
	
	/**
	 * Get the value encoded according to its type
	 * 
	 * @return mixed
	 */
	public function encodedValue()
	{
		return $this->definition
			? $this->definition->encode($this->value)
			: $this->value;
	}
}