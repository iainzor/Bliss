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
		$this->call($this, "preExecute");
		
		$options = getopt("p:");
		$route = $this->router()->find($options["p"]);
		$params = $route->params();
		$params["format"] = "cli";
		
		
		$this->execute($params);
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