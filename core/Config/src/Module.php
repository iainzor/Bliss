<?php
namespace Config;

class Module extends \Bliss\Module\AbstractModule
{
	/**
	 * @var \Config\Config
	 */
	private $config;
	
	/**
	 * Get a configuration object for a namespace
	 * 
	 * @param string $name
	 * @return \Config\Config
	 */
	public function get($name = null)
	{
		if (!isset($this->config)) {
			$this->_compileConfig();
		}
		
		if ($name !== null) {
			return $this->config->get($name);
		} else {
			return $this->config;
		}
	}
	
	/**
	 * Compile a configuration object for all available modules
	 * 
	 * @throws \UnexpectedValueException
	 */
	private function _compileConfig()
	{
		$this->config = new Config();
		
		foreach ($this->app->modules() as $module) {
			if ($module === $this) {
				continue;
			}
			$config = $module->config();
			
			$this->config->set($module->name(), $config);
			
			if ($module instanceof ProviderInterface) {
				$module->initConfig($this->config);
			}
		}
	}
}