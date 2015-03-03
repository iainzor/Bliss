<?php
namespace Bliss;

class AutoLoader
{
	/**
	 * @var array
	 */
	private $namespaces = [];
	
	/**
	 * Constructor
	 * 
	 * Registers the instance as the system's autoloader
	 */
	public function __construct()
	{
		spl_autoload_register([$this, "load"]);
	}
	
	/**
	 * Register a namespace
	 * 
	 * @param string $namespace
	 * @param string $rootPath
	 */
	public function registerNamespace($namespace, $rootPath)
	{
		if (!is_dir($rootPath)) {
			throw new \Exception("Invalid directory: {$rootPath}");
		}
		
		$this->namespaces[$namespace] = $rootPath;
	}
	
	/**
	 * Attempt to load the class provided
	 * 
	 * @param string $className
	 */
	public function load($className)
	{
		$parts = explode("\\", $className);
		$rootNamespace = array_shift($parts);
		
		if (isset($this->namespaces[$rootNamespace])) {
			$rootPath = $this->namespaces[$rootNamespace];
			$filePath = $rootPath ."/". implode("/", $parts) .".php";
			
			if (is_file($filePath)) {
				require_once $filePath;
			}
		}
	}
}