<?php
namespace Bliss\Module;

use Bliss\App\Container as App,
	Bliss\String;

class Registry implements \Iterator
{
	/**
	 * @var \Bliss\App\Container
	 */
	private $app;
	
	/**
	 * @var array
	 */
	private $modules = [];
	
	/**
	 * Constructor
	 * 
	 * @param \Bliss\App\Container $app
	 */
	public function __construct(App $app)
	{
		$this->app = $app;
	}
	
	/**
	 * Register a directory containing multiple modules
	 * 
	 * @param string $dirname
	 * @throws \Exception
	 */
	public function registerModulesDirectory($dirname)
	{
		if (!is_dir($dirname)) {
			throw new \Exception("Invalid directory: {$dirname}");
		}
		
		$this->app->log("Registering '{$dirname}' as a directory containing modules");
		
		foreach (new \DirectoryIterator($dirname) as $dir) {
			if ($dir->isDir() && !$dir->isDot()) {
				$this->registerModule($dir->getBasename(), $dir->getPathname());
			}
		}
	}
	
	/**
	 * Register a module with the registry
	 * 
	 * @param string $namespace
	 * @param string $dirname
	 * @throws \Exception
	 */
	public function registerModule($namespace, $dirname)
	{
		if (!is_dir($dirname)) {
			throw new \Exception("Invalid directory: {$dirname}");
		}
		
		$this->app->autoloader()->registerNamespace($namespace, $dirname ."/src");
		$this->app->log("Registering module '{$namespace}'");
		
		$moduleName = String::hyphenate($namespace);
		$className = $namespace ."\\Module";
		
		$this->modules[$moduleName] = [
			"className" => $className,
			"rootPath" => $dirname,
			"instance" => null
		];
	}
	
	/**
	 * Attempt to get a module by its name
	 * 
	 * @param string $moduleName
	 * @return \Bliss\Module\ModuleInterface
	 * @throws \Exception
	 */
	public function get($moduleName)
	{
		if (isset($this->modules[$moduleName])) {
			$config = $this->modules[$moduleName];
			$instance = $config["instance"];
			
			if ($instance === null) {
				$this->app->log("Creating module instance for '{$moduleName}'");
				
				$className = $config["className"];
				$rootPath = $config["rootPath"];
				$instance = new $className($this->app, $rootPath, $moduleName);

				if (!($instance instanceof ModuleInterface)) {
					throw new \Exception("Module '{$moduleName}' must be an instance of Bliss\\Module\\ModuleInterface");
				}
				
				$this->modules[$moduleName]["instance"] = $instance;
			}
			
			return $instance;
		}
		
		throw new \Exception("Module '{$moduleName}' could not be found", 404);
	}
	
	/**
	 * Implementation of \Iterator
	 */
		public function current() { $name = key($this->modules); return $this->get($name); }
		public function key() { return key($this->modules); }
		public function next() { return next($this->modules); }
		public function rewind() { return reset($this->modules); }
		public function valid() { $key = key($this->modules); return $key !== null && $key !== false; }

}