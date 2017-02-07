<?php
namespace Core;

class ConfigTuner
{
	/**
	 * @var AbstractApplication
	 */
	private $app;
	
	/**
	 * @var Config
	 */
	private $config;
	
	/**
	 * @param AbstractApplication $app
	 * @param \Core\Config $config
	 */
	public function __construct(AbstractApplication $app, Config $config)
	{
		$this->app = $app;
		$this->config = $config;
	}
	
	public function moduleDirectories()
	{
		
	}
}