<?php
namespace Core;

class ModuleRegistry
{
	/**
	 * @var AbstractApplication
	 */
	private $app;
	
	/**
	 * @var ModuleDefinition[]
	 */
	private $modules = [];
	
	/**
	 * Constructor
	 * 
	 * @param AbstractApplication $app
	 */
	public function __construct(AbstractApplication $app)
	{
		$this->app = $app;
	}
	
	/**
	 * Register a directory as a module.  The only thing that qualifies a directory as
	 * a module is having a /src directory.
	 * 
	 * Returns the newly created module definition on success or FALSE on failure.
	 * 
	 * @param string $path
	 * @return ModuleDefinition|false
	 */
	public function registerDirectory($path)
	{
		$path = str_replace(["/", "\\"], DIRECTORY_SEPARATOR, $path);
		$srcRoot = $path . DIRECTORY_SEPARATOR ."src";
		$filename = $srcRoot . DIRECTORY_SEPARATOR ."Module.php";
		$parts = explode(DIRECTORY_SEPARATOR, $path);
		$namespace = array_pop($parts);
		$className = $namespace ."\\Module";
		
		if (is_dir($srcRoot)) {
			$def = new ModuleDefinition($namespace, $path);
			$this->modules[strtolower($namespace)] = $def;
			$this->app->autoLoader()->registerNamespace($namespace, $path);
			
			return $def;
		}
		
		return false;
	}
	
	public function registerAll(string $directory, array $moduleNames = null)
	{
		foreach (new \DirectoryIterator($directory) as $item) {
			if (!$item->isDot() && $item->isDir()) {
				$name = $item->getBasename();
				
				if ($moduleNames === null || in_array($name, $moduleNames)) {
					$this->registerDirectory($item->getPathname());
				}
			}
		}
	}
	
	/**
	 * Get the definition of a module using its name
	 * 
	 * @param string $moduleName
	 * @return \Core\ModuleDefinition
	 * @throws \Exception
	 */
	public function module(string $moduleName) : ModuleDefinition
	{
		$key = strtolower($moduleName);
		if (!isset($this->modules[$key])) {
			throw new \Exception("Could not find module '{$moduleName}'");
		}
		
		return $this->modules[$key];
	}
	
	/**
	 * Perform a callback function on each ModuleDefinition in the registry
	 * 
	 * @param callable $callback
	 */
	public function each(callable $callback) 
	{
		foreach ($this->modules as $i => $module) {
			call_user_func($callback, $module, $i);
		}
	}
}