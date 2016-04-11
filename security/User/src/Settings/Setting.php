<?php
namespace User\Settings;

use Database\Model\AbstractModel;

class Setting extends AbstractModel
{
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
	public function value($value = null)
	{
		return $this->getSet("value", $value);
	}
}