<?php
namespace Core;

class Config
{
	/**
	 * @var AbstractApplication
	 */
	private $app;
	
	/**
	 * Constructor
	 * 
	 * @param \Core\AbstractApplication $app
	 */
	public function __construct(AbstractApplication $app)
	{
		$this->app = $app;
	}
	
	/**
	 * Load configuration data from one or more files.  Subsequent files will
	 * overwrite any existing values
	 * 
	 * @param string|array $files
	 */
	public function load($files)
	{
		$this->data = [];
		
		if (!is_array($files)) {
			$files = [$files];
		}
		
		foreach ($files as $file) {
			if (is_file($file)) {
				$fileData = include $file;
				
				if (!is_array($fileData)) {
					throw new \Exception("Config file must return an array of properties in '{$file}'");
				}
				
				$this->data = array_merge($this->data, $fileData);
			}
		}
		
		$this->app->moduleRegistry()->each(function(ModuleDefinition $def) {
			$instance = $def->instance($this->app);
			
			if ($instance instanceof ConfigurableModuleInterface) {
				$section = get_class($instance);
				$data = isset($this->data[$section]) ? $this->data[$section] : [];
				
				$instance->configure($this->app, new ModuleConfig($def, $data));
			}
		});
	}
	
}