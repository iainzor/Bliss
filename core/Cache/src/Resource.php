<?php
namespace Cache;

class Resource
{
	/**
	 * @var Driver\DriverInterface
	 */
	private $driver;
	
	/**
	 * @var string
	 */
	private $key;
	
	/**
	 * @var int
	 */
	private $lifetime = 30;
	
	/**
	 * @var boolean
	 */
	private $isLoaded = false;
	
	/**
	 * @var mixed
	 */
	private $contents;
	
	/**
	 * Constructor
	 * 
	 * @param Driver\DriverInterface $driver
	 * @param string $key
	 * @param int $lifetime
	 */
	public function __construct(Driver\DriverInterface $driver, $key, $lifetime = 30)
	{
		$this->driver = $driver;
		$this->key = $key;
		$this->lifetime($lifetime);
	}
	
	/**
	 * Get or set the lifetime of the resource (in seconds)
	 * 
	 * @param int $seconds
	 * @return int
	 */
	public function lifetime($seconds = null)
	{
		if ($seconds !== null) {
			$this->lifetime = (int) $seconds;
		}
		return $this->lifetime;
	}
	
	/**
	 * Get or set the contents of the resource
	 * 
	 * @param mixed $contents
	 * @return mixed
	 */
	public function contents($contents = null)
	{
		if ($contents !== null) {
			$this->contents = $contents;
		}
		if (!$this->isLoaded && !isset($this->contents)) {
			$this->load();
		}
		return $this->contents;
	}
	
	/**
	 * Check if the resource is expired
	 * 
	 * @return boolean
	 */
	public function isExpired()
	{
		return $this->driver->isExpired($this->key, $this->lifetime);
	}
	
	/**
	 * Attempt to load the contents of the resource
	 * 
	 * @return mixed
	 */
	public function load()
	{
		$this->isLoaded = true;
		$this->contents = $this->driver->get($this->key);
		
		return $this->contents;
	}
	
	/**
	 * Save the contents of the resource
	 * 
	 * @param mixed $contents
	 */
	public function save()
	{
		$this->driver->set($this->key, $this);
	}
}