<?php
namespace CmdLine;

require_once dirname(dirname(__DIR__)) ."/Core/src/AbstractApplication.php";

class Application extends \Core\AbstractApplication
{
	public function bootstrap() 
	{
		$this->di()->register($this);
	}
	
	public function run()
	{
		$options = getopt("m:c:a:");
		$moduleName = isset($options["m"]) ? $options["m"] : null;
		$controllerName = isset($options["c"]) ? $options["c"] : null;
		$actionName = isset($options["a"]) ? $options["a"] : null;
		
		if ($moduleName === null || $controllerName === null || $actionName === null) {
			throw new \Exception(
				"No module, controller, or action provided.\n" .
				"Usage: php exec.php -m [module] -c [controller] -a [action]\n"
			);
		}
		
		try {
			$result = $this->execute($moduleName, $controllerName, $actionName);
			var_dump($result);
		} catch (\Exception $e) {
			echo "Exception Encountered: {$e->getMessage()}\n\n";
			echo $e->getTraceAsString();
		}
	}
}
