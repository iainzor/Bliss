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
	protected $encoder = "strval";
	
	/**
	 * @var callable
	 */
	protected $decoder = "strval";
	
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
	 * Get or set the function used to encode the setting's value before 
	 * saving it to the database.
	 * 
	 * @param callable $encoder
	 * @return callable
	 */
	public function encoder($encoder = null)
	{
		return $this->getSet("encoder", $encoder);
	}
	
	/**
	 * Encode a value using the definition's encoding function
	 * 
	 * @param mixed $value
	 * @return mixed
	 * @throws \Exception
	 */
	public function encode($value)
	{
		if (!$this->encoder || !is_callable($this->encoder)) {
			throw new \Exception("Encoder must be callable");
		}
		return call_user_func($this->encoder, $value);
	}
	
	/**
	 * Get or set the function used to decode the setting's value from the database.
	 * 
	 * @param callable $decoder
	 * @return callable
	 */
	public function decoder($decoder = null)
	{
		return $this->getSet("decoder", $decoder);
	}
	
	/**
	 * Decode an encoded value using the definition's decode function
	 * 
	 * @param mixed $value
	 * @return mixed
	 * @throws \Exception
	 */
	public function decode($value) 
	{
		if (!$this->decoder || !is_callable($this->decoder)) {
			throw new \Exception("Decoder must be callable");
		}
		return call_user_func($this->decoder, $value);
	}
	
	/**
	 * Create a new default Setting instance from the definition
	 * 
	 * @return Setting
	 */
	public function toSetting()
	{
		$setting = new Setting($this);
		$setting->key($this->key());
		$setting->value($this->defaultValue());
		
		return $setting;
	}
}