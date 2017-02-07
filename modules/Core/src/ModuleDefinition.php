<?php
namespace Core;

class ModuleDefinition
{
	/**
	 * @var string
	 */
	private $namespace;
	
	/**
	 * @var string
	 */
	private $rootDir;
	
	/**
	 *
	 * @var AbstractModule
	 */
	private $instance;
	
	/**
	 * Constructor
	 * 
	 * @param string $namespace
	 * @param string $rootDir
	 */
	public function __construct(string $namespace, string $rootDir)
	{
		$this->namespace = $namespace;
		$this->rootDir = $rootDir;
	}
	
	/**
	 * Get the module's instance
	 * 
	 * @param AbstractApplication $app
	 * @return mixed
	 * @throws \Exception
	 */
	public function instance(AbstractApplication $app)
	{
		if (!$this->instance) {
			$className = $this->namespace ."\\Module";
			
			if (!class_exists($className)) {
				throw new \Exception("This module is registered as a library only.");
			}
			
			$this->instance = $app->di()->create($className);
		}
		return $this->instance;
	}
}