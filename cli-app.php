<?php
include "bliss-app.php";

use Response\Format\CLIFormat,
	Acl\Acl,
	Acl\Permission\RegexPermission;

class BlissCLIApp extends BlissApp
{
	public static function create($name, $rootPath, $environment = self::ENV_PRODUCTION) 
	{
		return parent::create($name, $rootPath, $environment);
	}
	
	public function run() 
	{
		global $argv;
		
		ob_end_clean();
		
		try {
			if (count($argv) < 2) {
				throw new \Exception("Missing uri argument");
			}

			$requestPath = array_pop($argv);
			$requestParams = [];
			
			if (preg_match("/(.*)\?(.*)$/i", $requestPath, $matches)) {
				$requestPath = $matches[1];
				parse_str($matches[2], $requestParams);
			}
			
			$this->call($this, "preExecute", [
				"uri" => $requestPath
			]);
			
			$options = getopt("s:");
			$route = $this->router()->find($requestPath);
			$params = array_merge($route->params(), $requestParams);
			$params["format"] = "cli";
			
			$response = $this->execute($params);
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
				}
			} else {
				echo $body;
			}
			
		} catch (\Exception $e) {
			$script = filter_input(INPUT_SERVER, "SCRIPT_NAME");
			echo "usage: php {$script} [-s section=value] <uri>\r\n\r\n";
			
			echo "ERROR\r\n";
			echo $e->getMessage() ."\r\n";
			echo $e->getTraceAsString() ."\r\n";
		}
	}
	
	public function preExecute($uri, \Response\Module $response, \Request\Module $request, \User\Module $users)
	{
		$users->user()->role()->addPermission(
			new RegexPermission("^.*$", [
				Acl::CREATE => true,
				Acl::READ => true,
				Acl::UPDATE => true,
				Acl::DELETE => true
			])
		);
		$request->setUri($uri);
		$response->format("cli", new CLIFormat());
		$response->sendHeaders(false);
		
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