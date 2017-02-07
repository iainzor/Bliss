<?php
namespace Core;

require_once __DIR__ ."/AutoLoader.php";

abstract class AbstractApplication
{
	/**
	 * @var AutoLoader
	 */
	private $autoLoader;
	
	/**
	 * @var ModuleRegistry
	 */
	private $moduleRegistry;
	
	/**
	 * @var DI
	 */
	private $di;
	
	/**
	 * @var Config
	 */
	private $config;
	
	final public function __construct()
	{
		$this->autoLoader = new AutoLoader(DIRECTORY_SEPARATOR . "src");
		$this->autoLoader->registerNamespace("Core", dirname(__DIR__));
		spl_autoload_register([$this->autoLoader, "load"], true);
		
		$this->moduleRegistry = new ModuleRegistry($this);
		$this->bootstrap();
	}
	
	abstract public function bootstrap();
	
	/**
	 * Gets the application's autoloader instance.  
	 * 
	 * @return AutoLoader
	 */
	final public function autoLoader()
	{
		return $this->autoLoader;
	}
	
	/**
	 * @return ModuleRegistry
	 */
	final public function moduleRegistry()
	{
		return $this->moduleRegistry;
	}
	
	/**
	 * @return ConfigTuner
	 */
	final public function configure()
	{
		return new ConfigTuner($this->config());
	}
	
	/**
	 * @return Config
	 */
	final public function config()
	{
		if (!$this->config) {
			$this->config = new Config($this);
		}
		return $this->config;
	}
	
	/**
	 * Get the application's dependency injector
	 * 
	 * @return DI
	 */
	final public function di()
	{
		if (!$this->di) {
			$this->di = new DI();
			$this->di->register(self::class, function() {
				return $this;
			});
		}
		return $this->di;
	}
	
	final public function execute(string $module, string $controller, string $action, array $params = [])
	{
		$moduleDef = $this->moduleRegistry()->module($module);
		$moduleInstance = $moduleDef->instance($this);
		
		//print_r($moduleInstance);
	}
}