<?php
namespace Core;

class AutoLoader
{
	private $prefix = null;
	
	/**
	 *
	 * @var array A mapping of namespaces to their root directories (['Core' => dirname(__DIR__)])
	 */
	private $namespaces = [];
	
	/**
	 * Constructor
	 * 
	 * @param string $prefix The string prefix that will be added between the namespace and the file path
	 */
	public function __construct($prefix = null) 
	{
		$this->prefix = $prefix;
	}
	
	/**
	 * Map a single namespace to a directory
	 * 
	 * @param string $namespace
	 * @param string $rootDir
	 * @return $this
	 */
	public function registerNamespace($namespace, $rootDir)
	{
		$this->namespaces[$namespace] = $rootDir;
		return $this;
	}
	
	/**
	 * Remove a namespace from the autoloader
	 * 
	 * @param string $namespace
	 * @return $this
	 */
	public function unregisterNamespace($namespace)
	{
		unset($this->namespaces[$namespace]);
		return $this;
	}
	
	/**
	 * Attempt to load a class
	 * 
	 * @param string $className
	 */
	public function load($className)
	{
		$parts = explode("\\", $className);
		if (count($parts) > 1) {
			$namespace = array_shift($parts);
			if (isset($this->namespaces[$namespace])) {
				$root = $this->namespaces[$namespace];
				$path = $root . $this->prefix . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $parts) .".php";
				
				if (is_file($path)) {
					require_once $path;
				}
			}
		}
	}
}