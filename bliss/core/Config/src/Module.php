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
	 * @param string $namespace
	 * @return \Config\Config
	 */
	public function get($namespace)
	{
		if (!isset($this->config)) {
			$this->_compileConfig();
		}
		
		return $this->config->get($namespace);
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
			$filename = $module->resolvePath("config/module.php");
			$data = [];
			
			if (is_file($filename)) {
				$data = include $filename;	
			}
			
			if (!is_array($data)) {
				throw new \UnexpectedValueException("Module configuration file for ". $module->name() ." must return an array");
			}
			
			$this->config->set($module->name(), $data);
			
			if ($module instanceof ProviderInterface) {
				$module->initConfig($this->config);
			}
		}
	}
}