<?php
namespace User\Settings;

use Bliss\Component;

class Definition extends Component
{
	/**
	 * @var string
	 */
	protected $key;
	
	/**
	 * @var mixed
	 */
	protected $defaultValue;
	
	/**
	 * @var callable
	 */
	protected $valueParser;
	
	/**
	 * Get or set the setting's key name
	 * 
	 * @param string $key
	 * @return string
	 */
	public function key($key = null)
	{
		return $this->getSet("key", $key);
	}
	
	/**
	 * Get or set the default value for this setting
	 * 
	 * @param mixed $value
	 * @return mixed
	 */
	public function defaultValue($value = null)
	{
		return $this->getSet("defaultValue", $value);
	}
	
	/**
	 * Get or set the callable function used to parse this setting's value
	 * 
	 * @param callable $parser
	 * @return callable
	 */
	public function valueParser($parser = null)
	{
		return $this->getSet("valueParser", $parser);
	}
	
	/**
	 * Parse the value using the definition's value parser
	 * 
	 * @param mixed $value
	 * @return mixed
	 * @throws \Exception
	 */
	public function parse($value)
	{
		if (!$this->valueParser) {
			return $value;
		}
		if (!is_callable($this->valueParser)) {
			throw new \Exception("Value parser must be callable");
		}
		return call_user_func($this->valueParser, $value);
	}
	
	/**
	 * Create a new default Setting instance from the definition
	 * 
	 * @return Setting
	 */
	public function toSetting()
	{
		$setting = new Setting();
		$setting->key($this->key());
		$setting->value($this->defaultValue());
		
		return $setting;
	}
}