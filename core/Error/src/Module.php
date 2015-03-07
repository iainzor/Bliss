<?php
namespace Error;

use Response\Format\InvalidFormatException,
	Response\Format\GenericFormat;

class Module extends \Bliss\Module\AbstractModule implements ErrorHandlerInterface
{
	public function init() 
	{
		set_error_handler([$this, "handleError"]);
		set_exception_handler([$this, "handleException"]);
	}
	
	public function handleError($number, $string, $file, $line)
	{
		throw new \Exception("Error '{$string}' in file '{$file}' on line '{$line}'", $number);
	}

	public function handleException(\Exception $e) 
	{
		ob_end_clean();
		
		$response = $this->app->response();
		$request = $this->app->request();
		$code = $e->getCode();
		
		switch ($code) {
			case 0: 
				$code = 500;
			default:
				$response->setCode($code);
				break;
		}
		
		if ($e instanceof InvalidFormatException) {
			$request->setFormat(null);
		}
		
		$ext = $request->getFormat();
		$format = $response->format($ext);
		if ($format instanceof GenericFormat) {
			$ext = null;
		}
		
		$this->app->execute([
			"module" => "error",
			"controller" => "error",
			"action" => "handle",
			"format" => $ext,
			"exception" => $e
		]);
		
	}
}