<?php
namespace User\Session;

use Bliss\Component;

class Config extends Component 
{
	const NAME = "name";
	const DOMAIN = "domain";
	const PATH = "path";
	const SECURE = "secure";
	const HTTP_ONLY = "httpOnly";
	
	/**
	 * @var string
	 */
	protected $name = "user_session";
	
	/**
	 * @var string
	 */
	protected $domain = "";
	
	/**
	 * @var string
	 */
	protected $path = "/";

	/**
	 * @var boolean
	 */
	protected $secure = false;
	
	/**
	 * @var boolean
	 */
	protected $httpOnly = false;
	
	/**
	 * Get or set the session name
	 * 
	 * @param string $name
	 * @return string
	 */
	public function name($name = null)
	{
		return $this->getSet("name", $name);
	}
	
	/**
	 * Get or set the session domain
	 * 
	 * @param string $domain
	 * @return string
	 */
	public function domain($domain = null)
	{
		return $this->getSet("domain", $domain);
	}
	
	/**
	 * Get or set the session's path
	 * 
	 * @param string $path
	 * @return string
	 */
	public function path($path = null)
	{
		return $this->getSet("path", $path);
	}
	
	/**
	 * Get or set whether the session is secure only
	 * 
	 * @param boolean $secure
	 * @return boolean
	 */
	public function secure($secure = null)
	{
		return $this->getSet("secure", $secure, self::VALUE_BOOLEAN);
	}
	
	/**
	 * Get or set whether the session is HTTP only
	 * 
	 * @param boolean $httpOnly
	 * @return boolean
	 */
	public function httpOnly($httpOnly = null)
	{
		return $this->getSet("httpOnly", $httpOnly, self::VALUE_BOOLEAN);
	}
}