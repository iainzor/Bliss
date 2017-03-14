<?php
namespace Cache\Driver;

use Bliss\Component;

class MemcacheServer
{
	use \Bliss\GetSetTrait;
	
	/**
	 * @var string
	 */
	protected $host;
	
	/**
	 * @var int
	 */
	protected $port = 11211;
	
	/**
	 * @var boolean
	 */
	protected $isPersistent = true;
	
	/**
	 * @var int
	 */
	protected $weight = 1;
	
	/**
	 * @var int
	 */
	protected $timeout = 1;
	
	/**
	 * @var int
	 */
	protected $retryInterval = 15;
	
	/**
	 * Constructor
	 * 
	 * @param string $host
	 * @param int $port
	 * @param boolean $isPersistent
	 * @param int $weight
	 * @param int $timeout
	 * @param int $retryInterval
	 */
	public function __construct($host, $port = 11211, $isPersistent = true, $weight = 1, $timeout = 1, $retryInterval = 15)
	{
		$this->host($host);
		$this->port($port);
		$this->isPersistent($isPersistent);
		$this->weight($weight);
		$this->timeout($timeout);
		$this->retryInterval($retryInterval);
	}
	
	public function host($host = null)
	{
		return $this->getSet("host", $host);
	}
	
	public function port($port = null)
	{
		return $this->getSet("port", $port, Component::VALUE_INT);
	}
	
	public function isPersistent($flag = null)
	{
		return $this->getSet("isPersistent", $flag, Component::VALUE_BOOLEAN);
	}
	
	public function weight($weight = null)
	{
		return $this->getSet("weight", $weight, Component::VALUE_INT);
	}
	
	public function timeout($timeout = null)
	{
		return $this->getSet("timeout", $timeout, Component::VALUE_INT);
	}
	
	public function retryInterval($interval = null)
	{
		return $this->getSet("retryInterval", $interval, Component::VALUE_INT);
	}
}