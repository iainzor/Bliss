<?php
namespace Core;

class Config
{
	const DEFAULT_TIMEZONE = "core.defaultTimezone";
	
	/**
	 * @var array
	 */
	private $data = [];
	
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
				
				$this->data = array_merge_recursive($this->data, $fileData);
			}
		}
	}
	
	/**
	 * Configure an application with the data loaded into the config
	 * 
	 * @param \Core\AbstractApplication $app
	 */
	public function configure(AbstractApplication $app)
	{
		$app->moduleRegistry()->each(function(ModuleDefinition $def) use ($app) {
			$instance = $def->instance($app);
			$section = get_class($instance);
			$data = isset($this->data[$section]) ? $this->data[$section] : [];
			$def->config()->populate($data);
			
			if ($instance instanceof ConfigurableModuleInterface) {
				$instance->configure($app, $def->config()); //new ModuleConfig($def, $data));
			}
		});
	}
	
}