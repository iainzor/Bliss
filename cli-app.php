<?php
include "bliss-app.php";

class BlissCLIApp extends BlissApp
{
	public static function create($name, $rootPath, $environment = self::ENV_PRODUCTION) 
	{
		return parent::create($name, $rootPath, $environment);
	}
	
	public function run() 
	{
		$options = getopt("p:");
		$route = $this->router()->find($options["p"]);
		$params = $route->params();
		$params["format"] = "json";
		
		$this->execute($params);
	}

	public function startupExceptionHandler(\Exception $e) 
	{
		echo "Startup Error!\n";
		echo $e->getMessage() ."\n";
		echo $e->getTraceAsString();
		exit;
	}
}