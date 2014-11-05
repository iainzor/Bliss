<?php
namespace Bliss\Module;

use Bliss\App\Container as App,
	Bliss\Controller\Registry as ControllerRegistry;

abstract class AbstractModule implements ModuleInterface
{
	/**
	 * @var \Bliss\App\Container
	 */
	protected $app;
	
	/**
	 * @var string
	 */
	protected $rootPath;
	
	/**
	 * @var \Bliss\Controller\Registry
	 */
	private $controllers;
	
	/**
	 * @var \Config\Config
	 */
	private $config;
	
	/**
	 * @var string
	 */
	private $name;
	
	/**
	 * Constructor
	 * 
	 * @param \Bliss\App\Container $app
	 */
	public function __construct(App $app, $rootPath, $name)
	{
		$this->app = $app;
		$this->rootPath = $rootPath;
		$this->name = $name;
		$this->controllers = new ControllerRegistry($this);
	}
	
	/**
	 * Blank method to make it optionally implemented by child modules
	 */
	public function init()
	{}
	
	/**
	 * Get the module's name
	 * 
	 * @return string
	 */
	public function name()
	{
		return $this->name;
	}
	
	/**
	 * Get the module's parent application
	 * 
	 * @return \Bliss\App\Container
	 */
	public function app()
	{
		return $this->app;
	}

	/**
	 * Execute a set of parameters on a module
	 * 
	 * @param \Request\Module $request
	 * @return string|array
	 */
	public function execute(\Request\Module $request) 
	{
		$this->init();
		
		$controllerName = $request->getController();
		$controller = $this->controller($controllerName);
		
		$this->app->log("Executing controller: {$controllerName}");
		
		return $controller->execute($request);
	}
	
	/**
	 * Attempt to find a controller by its name
	 * 
	 * @param string $controllerName
	 * @return \Bliss\Controller\ControllerInterface
	 */
	public function controller($controllerName)
	{
		return $this->controllers->get($controllerName);
	}
	
	/**
	 * Resolve the full path for a segment relative to the module's root path
	 * 
	 * @param string $segment
	 * @return string
	 */
	public function resolvePath($segment)
	{
		return $this->rootPath ."/{$segment}";
	}
	
	/**
	 * Get configuration data from the module
	 * 
	 * @param string $namespace
	 * @return \Config\Config
	 */
	public function config($namespace = null)
	{
		$appConfig = $this->app->config();
		$moduleConfig = $appConfig->get($this->name());
		
		if ($namespace !== null) {
			return $moduleConfig->get($namespace);
		} else {
			return $moduleConfig;
		}
	}
}