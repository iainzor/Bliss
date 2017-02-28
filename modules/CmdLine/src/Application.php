<?php
namespace CmdLine;

require_once dirname(dirname(__DIR__)) ."/Core/src/AbstractApplication.php";

class Application extends \Core\AbstractApplication
{
	protected function bootstrap() 
	{
		$this->di()->register($this);
	}
	
	public function run()
	{
		$options = getopt("m:c:a:p::");
		$moduleName = isset($options["m"]) ? $options["m"] : null;
		$controllerName = isset($options["c"]) ? $options["c"] : null;
		$actionName = isset($options["a"]) ? $options["a"] : null;
		$queryString = isset($options["p"]) ? $options["p"] : "";
		$params = $this->_parseQueryString($queryString);
		
		if ($moduleName === null || $controllerName === null || $actionName === null) {
			throw new \Exception(
				"No module, controller, or action provided.\n" .
				"Usage: php exec.php -m [module] -c [controller] -a [action] [-p [params]]\n"
			);
		}
		
		try {
			$result = $this->execute($moduleName, $controllerName, $actionName, $params);
			echo $result;
		} catch (\Exception $e) {
			echo "Exception Encountered: {$e->getMessage()}\n\n";
			echo $e->getTraceAsString();
		}
	}
	
	private function _parseQueryString(string $query) : array
	{
		$pairs = explode("&", $query);
		$params = [];
		foreach ($pairs as $pair) {
			$parts = explode("=", $pair);
			$name = $parts[0];
			$value = isset($parts[1]) ? $parts[1] : null;
			$params[$name] = $value;
		}
		return $params;
	}
}
