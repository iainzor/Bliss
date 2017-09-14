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
	
	/**
	 * @var boolean
	 */
	protected $started = false;
	
	abstract protected function onStart();
	abstract protected function onStop();
	
	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->autoLoader = new AutoLoader(DIRECTORY_SEPARATOR . "src");
		$this->autoLoader->registerNamespace("Core", dirname(__DIR__));
		spl_autoload_register([$this->autoLoader, "load"], true);
		
		$this->moduleRegistry = new ModuleRegistry($this);
		$this->moduleRegistry->registerDirectory(dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR ."Common");
		
		$this->config = new Config($this);
		
		$this->di()->register($this->config);
		$this->di()->register($this->moduleRegistry);
	}
	
	final public function start()
	{
		if ($this->started === true) {
			throw new \Exception("Application has already been started");
		}
		
		$this->started = true;
		
		$this->moduleRegistry->each(function(ModuleDefinition $moduleDef) {
			$module = $moduleDef->instance($this);
			
			if ($module instanceof BootableModuleInterface) {
				$module->bootstrap($this);
			}
		});
		$this->config->configure($this);
		
		$this->onStart();
	}
	
	final public function stop()
	{
		$this->started = false;
		$this->onStop();
		exit;
	}
	
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
	 * @return Config
	 */
	final public function config()
	{
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
		if (!$this->started) {
			throw new \Exception("Application has not been started, call start() before executing a command");
		}
		
		$module = $this->moduleRegistry()->module($moduleName);
		$controller = $module->controller($controllerName);
		$action = $controller->action($actionName);
		$actionParams = new ActionParams($params);
		
		return $action->call($this, $actionParams);
	}
}