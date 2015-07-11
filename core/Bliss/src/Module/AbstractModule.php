<?php
namespace Bliss\Module;

use Bliss\App\Container as App,
	Bliss\Controller\Registry as ControllerRegistry,
	Bliss\Config,
	Bliss\Component;

abstract class AbstractModule extends Component implements ModuleInterface
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
	 * @var \Bliss\Config
	 */
	private $config;
	
	/**
	 * @var string
	 */
	private $name;
	
	/**
	 * @var boolean
	 */
	private $enabled = true;
	
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
		$this->config = $app->config()->get($name);
		
		$this->init();
		
		if ($this->config) {
			$this->applyConfig($this->config);
		}
		
	}
	
	/**
	 * Apply a configuration file to the module
	 * 
	 * @param Config $config
	 * @throws \InvalidArgumentException
	 */
	public function applyConfig(Config $config = null)
	{
		foreach ($config->data() as $name => $data) {
			if (method_exists($this, $name)) {
				call_user_func([$this, $name], $data);
			} else {
				throw new \InvalidArgumentException("Invalid configuration key: {$name}");
			}
		}
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
	 * Get or set whether the module is enabled
	 * 
	 * @param boolean $flag
	 * @return boolean
	 */
	public function enabled($flag = null) 
	{
		if ($flag !== null) {
			$this->enabled = (boolean) $flag;
		}
		return $this->enabled;
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
		if (!isset($this->config)) {
			$this->config = new \Config\Config();
			
			$file = $this->resolvePath("config/module.php");
			if (is_file($file)) {
				$data = include $file;
				$this->config->setData($data);
			}
		}
		
		if ($namespace !== null) {
			return $this->config->get($namespace);
		} else {
			return $this->config;
		}
	}
}