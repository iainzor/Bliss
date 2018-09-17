<?php
require_once "core/Bliss/src/App/Container.php";

/**
 * # Core modules
 * @method \Assets\Module assets() Assets module
 * @method \Bliss\Module bliss() Bliss module
 * @method \Config\Module config() Config module
 * @method \Database\Module database()
 * @method \Error\Module error() Error module
 * @method \Request\Module request() Request module
 * @method \Response\Module response() Response module
 * @method \Router\Module router() Router module
 * @method \View\Module view() View module
 * 
 * # Development Modules
 * @method \Docs\Module docs() Docs module
 * @method \Tests\Module tests() Tests module
 * 
 * # Authentication Modules
 * @method \Acl\Module acl() ACL module
 */
class BlissWebApp extends \Bliss\App\Container
{
	private $startTime;
	
	public function __construct($name, $rootPath) 
	{
		parent::__construct($name, $rootPath);
		
		$this->startTime = microtime(true);
		
		set_exception_handler([$this, "startupExceptionHandler"]);
	}
	/**
	 * @param string $name
	 * @param string $rootPath
	 * @return BlissWebApp
	 */
	public static function create($name, $rootPath, $environment = self::ENV_PRODUCTION)
	{
		date_default_timezone_set("UTC");
		error_reporting(-1);
		ini_set("display_errors", true);
		ini_set("display_startup_errors", true);
		
		if (session_id() === "") {
			session_start();
		}
		
		// Create the application container
		$instance = new self($name, $rootPath);
		$instance->environment($environment);
		$instance->autoloader()->registerNamespace("Bliss", __DIR__ ."/core/Bliss/src");
		$instance->moduleRegistry()->registerModulesDirectory(__DIR__ ."/core");
		$instance->moduleRegistry()->registerModulesDirectory(__DIR__ ."/web");
		$instance->moduleRegistry()->registerModulesDirectory(__DIR__ ."/security");
		$instance->moduleRegistry()->registerModulesDirectory(__DIR__ ."/vendors");
		
		if ($environment !== self::ENV_PRODUCTION) {
			$instance->moduleRegistry()->registerModulesDirectory(__DIR__ ."/development");
		}
		
		return $instance;
	}
	
	public function run()
	{
		// Setup the request
		$baseUrl = preg_replace("/^(.*)\/.*\.php$/i", "\\1/", filter_input(INPUT_SERVER, "SCRIPT_NAME"));
		$requestUri = preg_replace("/([^\?]*)\?(.*)$/i", "\\1", 
			substr(filter_input(INPUT_SERVER, "REQUEST_URI"), strlen($baseUrl))
		);
		$request = $this->request();
		$request->setUri($requestUri);
		$request->baseUrl($baseUrl);

		$router = $this->router();
		$route = $router->find($requestUri);

		$this->execute($route->params());
	}
	
	/**
	 * Handle exceptions that may happen when starting the application
	 * 
	 * @param \Exception $e
	 */
	public function startupExceptionHandler(\Exception $e)
	{
		ob_end_clean();
			
		header("HTTP/1.1 500");

		echo "<h1>Startup Error!</h1>";
		echo "<h3>". $e->getMessage() ."</h3>";

		if ($this->debugMode()) {
			echo "<h4>Error Trace</h4>";
			echo "<pre>". $e->getTraceAsString() ."</pre>";

			echo "<h4>Execution Log</h4>";
			echo "<pre>";
			foreach ($this->logs() as $i => $log) {
				$date = new \DateTime();
				$date->setTimestamp($log["time"]);

				printf("#%d\t%s\t%s\t(%s:%s)\n", 
					$i+1, 
					$date->format("Y-m-d H:i:s") .".". preg_replace("/^[0-9]+\.([0-9]+)$/", "\\1", $log["time"]), 
					$log["message"], 
					$log["file"], 
					$log["line"]
				);
			}
			echo "</pre>";
		}
	}
	
	public function __destruct() 
	{
		if (!$this->hasQuit()) {
			try {
				$format = $this->request()->getFormat();

				if ($this->debugMode() && in_array($format, [null, "html"])) {
					echo "\n\n\n";
					echo "<!-- Total Execution Time .............. ". number_format((microtime(true) - $this->startTime) * 1000, 2) ." ms -->\n";
					echo "<!-- Total Memory Usage ................ ". number_format(memory_get_usage()/1024, 2) ." kb -->\n";

					echo "\n\n";
					echo "<!--                          -->\n";
					echo "<!--           Log            -->\n";
					echo "<!--                          -->\n";
					echo "<!--\n\n";
					foreach ($this->logs() as $log) {
						echo "\t{$log["message"]}\n";
					}
					echo "-->";
				}
			} catch (\Exception $e) {}
		}
	}
}