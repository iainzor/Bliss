<?php
namespace Cache;

class CacheItem
{
	/**
	 * @var \DateTime
	 */
	public $expires;
	
	/**
	 * @var string
	 */
	public $key;
	
	/**
	 * @var mixed
	 */
	public $contents;
	
	/**
	 * Constructor
	 * 
	 * @param string $key
	 * @param mixed $contents
	 */
	public function __construct(string $key, $contents = null, \DateTime $expires = null)
	{
		$this->key = $key;
		$this->contents = $contents;
		$this->expires = $expires;
	}
	
	/**
	 * Set the expiration date of the item
	 * 
	 * @param \DateTime $expires
	 */
	public function setExpires(\DateTime $expires)
	{
		$this->expires = $expires;
	}
	
	/**
	 * Clear the expiration date of the item
	 */
	public function clearExpires()
	{
		$this->expires = null;
	}
	
	/**
	 * Check if the item is expired
	 * 
	 * @return bool
	 */
	public function isExpired() : bool 
	{
		return isset($this->expires) ? $this->expires->getTimestamp() <= time() : false;
	}
}
