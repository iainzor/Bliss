<?php
namespace Bliss\App;

use Bliss\AutoLoader,
	Bliss\Module\Registry as ModuleRegistry,
	Bliss\String;

require_once dirname(__DIR__) ."/AutoLoader.php";
require_once dirname(__DIR__) ."/Module/Registry.php";

class Container 
{
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
	 * @var array
	 */
	private $params = [];
	
	/**
	 * Constructor
	 */
	public function __construct() 
	{
		$this->autoloader = new AutoLoader();
		$this->modules = new ModuleRegistry($this);
	}
	
	/**
	 * Get the application's module registry
	 * 
	 * @return \Bliss\Module\Registry
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
		$this->error()->handleError($number, $string, $file, $line);
	}
	
	/**
	 * Handle an exception
	 * 
	 * @param \Exception $e
	 */
	public function handleException(\Exception $e)
	{
		$this->error()->handleException($e);
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
		return $module;
	}
}