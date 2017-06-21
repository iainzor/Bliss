<?php
require_once "core/Bliss/src/App/Container.php";

abstract class BlissApp extends Bliss\App\Container
{
	/**
	 * @var int
	 */
	protected $startTime;
	
	public abstract function startupExceptionHandler($e);
	public abstract function run();
	
	/**
	 * @param string $name
	 * @param string $rootPath
	 */
	public function __construct($name, $rootPath) 
	{
		parent::__construct($name, $rootPath);
		
		$this->startTime = microtime(true);
		
		set_exception_handler([$this, "startupExceptionHandler"]);
	}
	
	/**
	 * @param string $name
	 * @param string $rootPath
	 * @return BlissApp
	 */
	public static function create($name, $rootPath, $environment = self::ENV_PRODUCTION)
	{
		date_default_timezone_set("UTC");
		error_reporting(E_ALL);
		ini_set("display_errors", true);
		ini_set("display_startup_errors", true);
		
		if (session_id() === "") {
			session_start();
		}
		
		// Create the application container
		$instance = new static($name, $rootPath);
		$instance->environment($environment);
		$instance->autoloader()->registerNamespace("Bliss", __DIR__ ."/core/Bliss/src");
		$instance->moduleRegistry()->registerModulesDirectory(__DIR__ ."/core");
		
		if ($environment !== self::ENV_PRODUCTION) {
			$instance->moduleRegistry()->registerModulesDirectory(__DIR__ ."/development");
		}
		
		return $instance;
	}
}