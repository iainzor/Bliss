<?php
namespace Sessions;

class Session
{
	/**
	 * @var string
	 */
	private $namespace;
	
	/**
	 * @var array
	 */
	private $data = [];
	
	/**
	 * @var \SessionHandlerInterface
	 */
	private $handler;
	
	/**
	 * @var boolean
	 */
	private $loaded = false;
	
	/**
	 * Constructor
	 * 
	 * @param string $namespace
	 * @param \SessionHandlerInterface $handler
	 */
	public function __construct(string $namespace, \SessionHandlerInterface $handler)
	{
		$this->namespace = $namespace;
		$this->handler = $handler;
	}
	
	private function init()
	{
		if (!session_id()) {
			session_start();
			session_set_save_handler($this->handler);
		}
	}
	
	/**
	 * Destructor
	 * 
	 * Saves the session to the handler
	 */
	public function __destruct() 
	{
		$this->save();
	}
	
	/**
	 * Load all data from the session namespace
	 * 
	 * @return array
	 */
	public function load() : array
	{
		$this->init();
		
		$this->loaded = true;
		$this->data = isset($_SESSION[$this->namespace]) ? $_SESSION[$this->namespace] : [];
		
		return $this->data;
	}
	
	/**
	 * Save all available data to the session namespace
	 */
	public function save()
	{
		$this->init();
		
		$_SESSION[$this->namespace] = $this->data;
	}
	
	/**
	 * Get a session value
	 * 
	 * @param string $key
	 * @param mixed $defaultValue
	 * @return mixed
	 */
	public function get(string $key, $defaultValue = null)
	{
		if (!$this->loaded) {
			$this->load();
		}
		
		return isset($this->data[$key]) ? $this->data[$key] : $defaultValue;
	}
	
	/**
	 * Set a session value
	 * 
	 * @param string $key
	 * @param mixed $value
	 */
	public function set(string $key, $value)
	{
		$this->data[$key] = $value;
	}
	
	/**
	 * Delete session data
	 * 
	 * @param string $key
	 */
	public function delete(string $key)
	{
		unset($this->data[$key]);
	}
}