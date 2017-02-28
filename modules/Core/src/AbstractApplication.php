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
		$this->moduleRegistry->registerDirectory(dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR ."Common");
		$this->bootstrap();
	}
	
	abstract protected function bootstrap();
	
	abstract public function run();
	
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
			$this->di->register($this);
		}
		return $this->di;
	}
	
	/**
	 * Find and execute a path to an action
	 * 
	 * @param string $moduleName
	 * @param string $controllerName
	 * @param string $actionName
	 * @param array $params
	 * @return mixed
	 */
	final public function execute(string $moduleName, string $controllerName, string $actionName, array $params = [])
	{
		$module = $this->moduleRegistry()->module($moduleName);
		$controller = $module->controller($controllerName);
		$action = $controller->action($actionName);
		$actionParams = new ActionParams($params);
		
		return $action->call($this, $actionParams);
	}
}