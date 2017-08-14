<?php
namespace Bliss\Module;

use Bliss\App\Container as App,
	Bliss\FileSystem\File;

class Registry
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
	 * @var array
	 */
	private $dirs = [];
	
	/**
	 * @var boolean
	 */
	private $isCache = false;
	
	/**
	 * @var \FileSystem\File
	 */
	private $cache;
	
	/**
	 * Constructor
	 * 
	 * @param \Bliss\App\Container $app
	 */
	public function __construct(App $app)
	{
		$this->app = $app;
		$this->cache = new File(
			$app->resolvePath("/files/cache/bliss.modules")
		);
		
		$this->_initCache();
	}
	
	public function __destruct() 
	{
		$this->_saveCache();
	}
	
	/**
	 * Register a directory containing multiple modules
	 * 
	 * @param string $dirname
	 * @throws \Exception
	 */
	public function registerModulesDirectory($dirname)
	{
		if (in_array($dirname, $this->dirs)) {
			return;
		}
		if (!is_dir($dirname)) {
			throw new \Exception("Invalid directory: {$dirname}");
		}
		
		$this->app->log("Registering '{$dirname}' as a directory containing modules");
		$this->dirs[] = $dirname;
		
		foreach (new \DirectoryIterator($dirname) as $dir) {
			if ($dir->isDir() && !$dir->isDot() && !preg_match("/^[\._]/", $dir->getFilename())) {
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
		
		$alias = strtolower(preg_replace("/[^a-z0-9]/i", "", $namespace));
		$className = $namespace ."\\Module";
		
		$this->modules[$alias] = [
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
		$alias = strtolower(preg_replace("/[^a-z0-9]/i", "", $moduleName));
		
		if (isset($this->modules[$alias])) {
			$config = $this->modules[$alias];
			$instance = $config["instance"];
			
			if ($instance === null) {
				$this->app->log("Creating module instance for '{$moduleName}'");
				
				$className = $config["className"];
				$rootPath = $config["rootPath"];
				$instance = new $className($this->app, $rootPath, $moduleName);

				if (!($instance instanceof ModuleInterface)) {
					throw new \Exception("Module '{$instance->name()}' must be an instance of Bliss\\Module\\ModuleInterface");
				}
				
				$this->modules[$alias]["instance"] = $instance;
			}
			
			return $instance;
		}
		
		throw new ModuleNotFoundException("Module '{$moduleName}' could not be found", 404);
	}
	
	/**
	 * Get all registered modules
	 * 
	 * @return \Bliss\Module\ModuleInterface[]
	 */
	public function all()
	{
		$modules = [];
		foreach ($this->modules as $alias => $data) {
			$modules[] = $this->get($alias);
		}
		return $modules;
	}
	
	/**
	 * Initialize the
	 */
	private function _initCache()
	{
		return;
		
		if ($this->cache->exists()) {
			$this->cache->delete();
			
			$data = unserialize($this->cache->contents());
			
			if (is_array($data) && !empty($data)) {
				$this->dirs = $data["dirs"];
				$this->modules = $data["modules"];
				$this->isCache = true;
				
				foreach ($this->modules as $module) {
					$namespace = preg_replace("/^(.*)\\\\Module$/i", "\\1", $module["className"]);
					$this->app->autoloader()->registerNamespace($namespace, $module["rootPath"] ."/src");
				}
			}
		}
	}
	
	/**
	 * Cache the registry's data
	 */
	private function _saveCache()
	{
		return;
		
		$data = [
			"dirs" => $this->dirs
		];
		$modules = [];
		foreach ($this->modules as $alias => $module) {
			$modules[$alias] = [
				"className" => $module["className"],
				"rootPath" => $module["rootPath"],
				"instance" => null
			];
		}
		$data["modules"] = $modules;
		
		$this->cache->write(serialize($data));
	}
}