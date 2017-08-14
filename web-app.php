<?php
include "bliss-app.php";


if (!class_exists("Error")) {	
	class Error extends Exception {}
}

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
class BlissWebApp extends BlissApp
{
	
	/**
	 * @var string
	 */
	protected $url;
	
	/**
	 * Get or set the application's URL
	 * 
	 * @param string $url
	 * @return string
	 */
	public function url($url = null)
	{
		return $this->getSet("url", $url);
	}
	
	/**
	 * @param string $name
	 * @param string $rootPath
	 * @param string $environment
	 * @return BlissWebApp
	 */
	public static function create($name, $rootPath, $environment = self::ENV_PRODUCTION) 
	{
		$instance = parent::create($name, $rootPath, $environment);
		$instance->moduleRegistry()->registerModulesDirectory(__DIR__ ."/web");
		$instance->moduleRegistry()->registerModulesDirectory(__DIR__ ."/security");
		$instance->moduleRegistry()->registerModulesDirectory(__DIR__ ."/vendors");
		
		return $instance;
	}
	
	public function run()
	{
		$baseUrl = "/";
		$baseMatch = preg_match("/^(.*)\/((.+)\.php)$/i", filter_input(INPUT_SERVER, "SCRIPT_NAME"), $baseMatches);
		if (isset($baseMatches[1])) {
			$baseUrl = $baseMatches[1] ."/";
		}
		
		$_uri = $this->_server("REQUEST_URI");
		$_httpHost = $this->_server("HTTP_HOST");
		$_https = $this->_server("HTTPS");
		
		$requestUri = preg_replace("/^([^\?]+).*/i", "$1", substr($_uri, strlen($baseUrl)));
		
		if (!$this->url) {
			$this->url = ($_https ? "https" : "http") ."://". $_httpHost . $baseUrl;
		}
		/*
		echo "<pre>";
		var_dump($baseMatch);
		print_r($baseMatches);
		
		
		var_dump($baseUrl);
		var_dump($requestUri);
		exit;
		//*/
		
		$request = $this->request();
		$request->setUri($requestUri);
		$request->baseUrl($baseUrl);

		$router = $this->router();
		$route = $router->find($requestUri);
		
		$this->execute($route->params());
	}
	
	/**
	 * Get a value from the server input
	 * 
	 * @param string $field
	 * @return string
	 */
	private function _server($field)
	{
		$value = filter_input(INPUT_SERVER, $field);
		if (!$value) {
			$value = isset($_SERVER[$field])? $_SERVER[$field] : null;
		}
		return $value;
	}
	
	/**
	 * Handle exceptions that may happen when starting the application
	 * 
	 * @param \Exception $e
	 */
	public function startupExceptionHandler($e)
	{
		ob_end_clean();
			
		header("HTTP/1.1 500");

		echo "<h1>Startup Error!</h1>";
		echo "<h3>". $e->getMessage() ."</h3>";
		
		$showConsole = $this->error()->showConsole();
		$showTrace = $this->error()->showTrace();
		if ($this->debugMode()) {
			$showConsole = $showTrace = true;
		}

		if ($showTrace) {
			echo "<h4>Error Trace</h4>";
			echo "<pre>". $e->getTraceAsString() ."</pre>";
		}
		
		if ($showConsole) {
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