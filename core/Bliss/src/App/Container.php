<?php
namespace Bliss\App;

use Bliss\AutoLoader,
	Bliss\Module\Registry as ModuleRegistry,
	Bliss\Module\ModuleInterface,
	Bliss\String,
	Bliss\Config;

require_once dirname(__DIR__) ."/AutoLoader.php";
require_once dirname(__DIR__) ."/Module/Registry.php";
require_once dirname(__DIR__) ."/Component.php";
require_once dirname(__DIR__) ."/String.php";
require_once dirname(__DIR__) ."/Config.php";
require_once dirname(__DIR__) ."/FileSystem/File.php";
require_once dirname(__DIR__) ."/FileSystem/Exception.php";

class Container extends \Bliss\Component
{
	const ENV_DEVELOPMENT = "development";
	const ENV_PRODUCTION = "production";
	const ENV_STAGING = "staging";
	const ENV_TESTING = "testing";
	
	/**
	 * @var \Bliss\AutoLoader
	 */
	private $autoloader;
	
	/**
	 * @var \Bliss\Module\Registry
	 */
	private $moduleRegistry;
	
	/**
	 * @var array
	 */
	private $logs = [];
	
	/**
	 * @var string
	 */
	protected $name;
	
	/**
	 * @var string
	 */
	private $rootPath;
	
	/**
	 * @var string
	 */
	private $environment = self::ENV_PRODUCTION;
	
	/**
	 * @var \Config\Config
	 */
	private $config;
	
	/**
	 * @var boolean
	 */
	private $debugMode = false;
	
	/**
	 * Constructor
	 * 
	 * @param string $name The name of the application
	 */
	public function __construct($name, $rootPath) 
	{
		ob_start();
		
		$this->name = $name;
		$this->rootPath = $rootPath;
		$this->autoloader = new AutoLoader();
		$this->moduleRegistry = new ModuleRegistry($this);
		
		if (!is_dir($this->resolvePath("files"))) {
			throw new \Exception("Directory could not be found: ". $this->resolvePath("files"));
		}
	}
	
	/**
	 * Resolve the path to the partial path relative to the application root directory
	 * 
	 * @param string $partial
	 * @return string
	 */
	public function resolvePath($partial = null)
	{
		return $this->rootPath ."/". $partial;
	}
	
	/**
	 * Set the name of the application
	 * 
	 * @param string $name
	 */
	public function setTitle($name)
	{
		$this->name = $name;
	}
	
	/**
	 * Get the application's title
	 * 
	 * @return string
	 */
	public function getTitle()
	{
		return $this->name;
	}
	
	/**
	 * Get or set the application's environment
	 * 
	 * @param string $env
	 * @return string
	 */
	public function environment($env = null)
	{
		if ($env !== null) {
			$this->environment = $env;
		}
		return $this->environment;
	}
	
	/**
	 * 
	 * @return type
	 */
	public function moduleRegistry()
	{
		return $this->moduleRegistry;
	}
	
	/**
	 * Get the application's module registry
	 * 
	 * @return \Bliss\Module\ModuleInterface[]
	 */
	public function modules()
	{
		return array_filter($this->moduleRegistry->all(), function(ModuleInterface $module) {
			return $module->enabled();
		});
	}
	
	/**
	 * Attempt to get a module instance
	 * 
	 * @param string $moduleName
	 * @return \Bliss\Module\ModuleInterface
	 * @throws \Exception
	 */
	public function module($moduleName)
	{
		$module = $this->moduleRegistry->get($moduleName);
		
		if (!$module->enabled()) {
			throw new \Exception("Inactive module: {$moduleName}");
		}
		
		return $module;
	}
	
	/**
	 * Get the application logs
	 * 
	 * @return array
	 */
	public function logs()
	{
		return $this->logs;
	}
	
	/**
	 * Add a log message to the application
	 * 
	 * @param string $message
	 */
	public function log($message)
	{	
		$trace = debug_backtrace();
		$caller = array_shift($trace);
		
		$this->logs[] = [
			"time" => microtime(true),
			"message" => $message,
			"file" => $caller["file"],
			"line" => $caller["line"]
		];
	}
	
	/**
	 * Get the application's autoloader
	 * 
	 * @return \Bliss\AutoLoader
	 */
	public function autoloader()
	{
		return $this->autoloader;
	}
	
	/**
	 * Attempt to execute a Route
	 * 
	 * @param array $params
	 * @return string
	 */
	public function execute(array $params = [])
	{
		$this->initConfig();
		
		$this->log("Executing parameters: ". json_encode($params));
		
		$response = $this->response();
		$request = $this->request();
		$request->reset();
		$request->setParams($params);
		
		$moduleName = $request->getModule();
		$module = $this->module($moduleName);
		$this->log("Executing module: {$moduleName}");
		$result = $module->execute($request);
		
		if (is_object($result) && method_exists($result, "toArray")) {
			$result = $result->toArray();
		}

		if (is_string($result)) {
			$response->setBody($result);
		} elseif (is_array($result)) {
			$response->setParams($result);
		} elseif ($result !== null) {
			throw new \Exception("Action must either return a string or array");
		}

		$response->send($request, $this->view());
	}
	
	/**
	 * Get the application's configuration object
	 * 
	 * @return \Config\Config
	 */
	public function config() 
	{
		if (!isset($this->config)) {
			$this->initConfig();
		}
		return $this->config;
	}
	
	/**
	 * Load all available configuration files into the current application's 
	 * config object
	 */
	private function initConfig()
	{
		$this->config = new Config();
		
		$files = [
			"config.php", 
			"config-". $this->environment .".php",
			"private/config.php",
			"private/config-". $this->environment .".php"
		];
		foreach ($files as $file) {
			$path = $this->resolvePath($file);
			
			if (is_file($path)) {
				$data = include $path;
				$this->config->merge($data);
			}
		}
		
		foreach ($this->config->data() as $name => $data) {
			if (method_exists($this, $name)) {
				call_user_func([$this, $name], $data);
			}
		}
	}
	
	/**
	 * Get or set whether the application is in debug mode
	 * 
	 * @param boolean $flag
	 * @return boolean
	 */
	public function debugMode($flag = null)
	{
		if ($flag !== null) {
			$this->debugMode = (boolean) $flag;
		}
		return $this->debugMode;
	}
	
	/**
	 * Call a method with all depencies injected
	 * 
	 * @param Object $object
	 * @param string $method
	 */
	public function call($object, $method)
	{
		$ref = new \ReflectionMethod($object, $method);
		$refParams = $ref->getParameters();
		$args = [];
		
		foreach ($refParams as $refParam) {
			$className = $refParam->getClass()->getName();
			if (preg_match("/^([a-z0-9]+).module$/i", $className, $matches)) {
				$args[] = $this->module($matches[1]);
			} else {
				throw new \InvalidArgumentException("Could not inject parameter '\${$refParam->getName()}' into '{$method}'");
			}
		}
		
		return call_user_func_array([$object, $method], $args);
	}
	
	/**
	 * Attempt to load modules dynamically 
	 * 
	 * @param string $name
	 * @param array $arguments
	 * @return \Bliss\Module\ModuleInterface
	 */
	public function __call($name, array $arguments) 
	{
		$moduleName = String::hyphenate($name);
		$module = $this->module($moduleName);
		
		if (method_exists($module, $name)) {
			$refMethod = new \ReflectionMethod($module, $name);
			if ($refMethod->getDeclaringClass()->getName() === get_class($module)) {
				return call_user_func_array([$module, $name], $arguments);
			}
		}
			
		return $module;
	}
	
	/**
	 * Override toArray to add each module with a public config to the exported array
	 * 
	 * @return array
	 */
	public function toArray() 
	{
		$data = parent::toArray();
		$modules = $this->modules();
		
		foreach ($modules as $module) {
			if ($module instanceof \Config\PublicConfigInterface) {
				$config = new \Config\Config();
				$module->populatePublicConfig($config);
				
				$data[$module->name()] = $config->toArray();
			}
		}
		
		return $data;
	}
}