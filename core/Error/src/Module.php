<?php
namespace Error;

use Response\Format\InvalidFormatException,
	Response\Format\GenericFormat;

class Module extends \Bliss\Module\AbstractModule implements ErrorHandlerInterface
{
	private $showTrace = false;
	private $showConsole = false;
	
	public function init() 
	{
		set_error_handler([$this, "handleError"]);
		set_exception_handler([$this, "handleException"]);
	}
	
	public function handleError($number, $string, $file, $line)
	{
		$this->handleException(
			new \Exception("Error '{$string}' in file '{$file}' on line '{$line}'", $number)
		);
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
	
	/**
	 * Get or set whether to show the error trace on error pages
	 * 
	 * @param boolean $flag
	 * @return boolean
	 */
	public function showTrace($flag = null)
	{
		if ($flag !== null) {
			$this->showTrace = (boolean) $flag;
		}
		return $this->showTrace;
	}
	
	/**
	 * Get or set whether to show the console log on error pages
	 * 
	 * @param boolean $flag
	 * @return boolean
	 */
	public function showConsole($flag = null)
	{
		if ($flag !== null) {
			$this->showConsole = (boolean) $flag;
		}
		return $this->showConsole;
	}
}