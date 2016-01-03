<?php
include "bliss-app.php";

use Response\Format\CLIFormat;

class BlissCLIApp extends BlissApp
{
	public static function create($name, $rootPath, $environment = self::ENV_PRODUCTION) 
	{
		return parent::create($name, $rootPath, $environment);
	}
	
	public function run() 
	{
		global $argv;
		
		$this->call($this, "preExecute");
		
		try {
			if (count($argv) < 2) {
				throw new \Exception("Missing uri argument");
			}

			$path = array_pop($argv);
			$options = getopt("s:");
			$route = $this->router()->find($path);
			$params = $route->params();
			$params["format"] = "cli";

			ob_start();
			$response = $this->execute($params);
			ob_end_clean();

			$body = $response->body();
			$rParams = $response->params();

			if (empty($body)) {
				if (isset($options["s"])) {
					$section = $options["s"];
					if (isset($rParams[$section])) {
						echo $rParams[$section];
					} else {
						throw new \Exception("Unknown section: {$section}");
					}
				} else {
					throw new \Exception(
						"No section specified...\r\n" .
						"Available sections: ". implode(", ", array_keys($rParams))
					);
				}
			} else {
				echo $body;
			}
		} catch (\Exception $e) {
			$script = filter_input(INPUT_SERVER, "SCRIPT_NAME");
			echo "usage: php {$script} [-s section=value] <uri>\r\n\r\n";
			
			echo "ERROR\r\n";
			echo $e->getMessage() ."\r\n";
		}
	}
	
	public function preExecute(\Response\Module $response, \Error\Module $error)
	{
		$error->enabled(false);
		$response->format("cli", new CLIFormat());
		
		set_error_handler([$this, "_handleError"]);
		set_exception_handler([$this, "_handleException"]);
	}

	public function startupExceptionHandler(\Exception $e) 
	{
		echo "Startup Error!\n";
		echo $e->getMessage() ."\n";
		echo $e->getTraceAsString();
		exit;
	}
	
	public function _handleError($num, $message)
	{
		echo "ERROR: {$message}\n";
	}
	
	public function _handleException(\Exception $e)
	{
		die($e->getMessage());
	}
}