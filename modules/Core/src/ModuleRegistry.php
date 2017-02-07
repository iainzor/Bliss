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
	 * Register one or more directories as modules.  This will recursively search
	 * downward for sub-modules.
	 * 
	 * @param string|array $directories
	 */
	public function registerDirectory($directories)
	{
		if (!is_array($directories)) {
			$directories = [$directories];
		}
		foreach ($directories as $dir) {
			if (!is_dir($dir)) { continue; }
			
			foreach (new \DirectoryIterator($dir) as $item) {
				if ($item->isDot() || $item->isFile()) {
					continue;
				}
				
				$this->_registerDirectory($item);
			}
		}
	}
	
	/**
	 * @param \DirectoryIterator $dir
	 */
	private function _registerDirectory(\DirectoryIterator $dir)
	{
		$root = $dir->getPathname();
		$srcRoot = $root . DIRECTORY_SEPARATOR ."src";
		$filename = $srcRoot . DIRECTORY_SEPARATOR ."Module.php";
		$parts = explode(DIRECTORY_SEPARATOR, $root);
		$namespace = array_pop($parts);
		$className = $namespace ."\\Module";
		
		if (is_dir($srcRoot)) {
			$def = new ModuleDefinition($namespace, $root);
			$this->modules[strtolower($namespace)] = $def;
			$this->app->autoLoader()->registerNamespace($namespace, $srcRoot);
			
			if (is_file($filename)) {
				require_once $filename;
				
				$classRef = new \ReflectionClass($className);
				$interfaces = $classRef->getInterfaceNames();
				if (in_array(BootableModuleInterface::class, $interfaces)) {
					call_user_func([$className, "bootstrap"], $this->app);
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
}