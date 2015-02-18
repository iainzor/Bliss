<?php
namespace Bliss\App;

use Bliss\AutoLoader,
	Bliss\Module\Registry as ModuleRegistry,
	Bliss\String;

require_once dirname(__DIR__) ."/AutoLoader.php";
require_once dirname(__DIR__) ."/Module/Registry.php";
require_once dirname(__DIR__) ."/Component.php";
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
	private $modules;
	
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
	 * Constructor
	 * 
	 * @param string $name The name of the application
	 */
	public function __construct($name, $rootPath) 
	{
		$this->name = $name;
		$this->rootPath = $rootPath;
		$this->autoloader = new AutoLoader();
		$this->modules = new ModuleRegistry($this);
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
	 * Get the application's module registry
	 * 
	 * @return \Bliss\Module\ModuleInterface[]
	 */
	public function modules()
	{
		return $this->modules;
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
		$module = $this->modules->get($moduleName);
		
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
		$this->loadConfig();
		
		$this->log("Executing parameters: ". json_encode($params));
		
		$response = $this->response();
		$request = $this->request();
		$request->reset();
		$request->setParams($params);
		
		$moduleName = $request->getModule();
		$module = $this->module($moduleName);
		$this->log("Executing module: {$moduleName}");
		$result = $module->execute($request);

		if (is_string($result)) {
			$response->setBody($result);
		} elseif (is_array($result)) {
			$response->setParams($result);
		} elseif ($result !== null) {
			throw new \Exception("Action must either return a string or array");
		}

		$response->send($request);
	}
	
	/**
	 * Handle a system error
	 * 
	 * @param int $number
	 * @param string $string
	 * @param string $file
	 * @param int $line
	 */
	public function handleError($number, $string, $file, $line) 
	{
		$this->log("ERROR: {$string}");
		$this->error()->handleError($number, $string, $file, $line);
	}
	
	/**
	 * Handle an exception
	 * 
	 * @param \Exception $e
	 */
	public function handleException(\Exception $e)
	{
		$this->log("EXCEPTION: {$e->getMessage()}");
		$this->error()->handleException($e);
	}
	
	/**
	 * Get the application's configuration object
	 * 
	 * @return \Config\Config
	 */
	public function config() 
	{
		if (!isset($this->config)) {
			$this->loadConfig();
		}
		return $this->config;
	}
	
	/**
	 * Load all available configuration files into the current application's 
	 * config object
	 */
	public function loadConfig()
	{
		$this->config = $this->module("config")->get();
		
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
		$modules = clone $this->modules();
		
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