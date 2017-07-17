<?php
namespace Cache;

use Core\ModuleConfig;

class Config
{
	const DRIVER_CLASS = "cache.driver.class";
	const DRIVER_OPTIONS = "cache.driver.options";
	const DEFAULT_LIFETIME = "cache.defaultLifetime";
	
	/**
	 * @var ModuleConfig
	 */
	private $config;
	
	/**
	 * Constructor
	 * 
	 * @param ModuleConfig $config
	 */
	public function __construct(ModuleConfig $config)
	{
		$this->config = $config;
	}
	
	/**
	 * Get the default lifetime, in seconds, for items stored in the cache
	 * 
	 * @return int
	 */
	public function defaultLifetime() : int 
	{
		return $this->config->get(self::DEFAULT_LIFETIME, 0);
	}
}