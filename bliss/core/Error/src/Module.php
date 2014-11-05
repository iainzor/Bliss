<?php
namespace Error;

class Module extends \Bliss\Module\AbstractModule
implements ErrorHandlerInterface
{
	const NAME = "error";
	
	public function getName() { return self::NAME; }
	
	public function handleError($number, $string, $file, $line)
	{
		throw new \Exception("Error '{$string}' in file '{$file}' on line '{$line}'", $number);
	}

	public function handleException(\Exception $e) 
	{
		$response = $this->app->response();
		$request = $this->app->request();
		$formatName = $request->getFormat();
		$format = $response->format($formatName);
		
		switch ($e->getCode()) {
			case 404:
				$response->setCode(404);
				break;
			default:
				$response->setCode(500);
				break;
		}
		
		if ($e instanceof \Response\Format\InvalidFormatException || !$format->requiresView()) {
			$request->setFormat(null);
		}
		
		$this->app->execute([
			"module" => "error",
			"controller" => "error",
			"action" => "handle",
			"format" => $request->getFormat(),
			"exception" => $e
		]);
	}
}