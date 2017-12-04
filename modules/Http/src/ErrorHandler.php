<?php
namespace Http;

class ErrorHandler
{
	/**
	 * @var Request
	 */
	private $request;
	
	/**
	 * @var Response
	 */
	private $response;
	
	/**
	 * @var boolean
	 */
	private $showTrace = false;
	
	/**
	 * Constructor
	 * 
	 * @param \Http\Request $request
	 * @param \Http\Response $response
	 */
	public function __construct(Request $request, Response $response)
	{
		$this->request = $request;
		$this->response = $response;
	}
	
	/**
	 * Set whether to show an error trace when outputting errors
	 * 
	 * @param bool $flag
	 */
	public function showTrace(bool $flag)
	{
		$this->showTrace = $flag;
	}
	
	/**
	 * Attach the error handler to the running application
	 */
	public function attach()
	{
		set_error_handler([$this, "handleError"]);
		set_exception_handler([$this, "handleException"]);
	}
	
	/**
	 * Detach the error handler from the running application
	 */
	public function detach()
	{
		restore_error_handler();
		restore_exception_handler();
	}
	
	/**
	 * Handle a system error
	 * 
	 * @param int $number
	 * @param string $message
	 * @param string $file
	 * @param int $line
	 * @param array $context
	 */
	public function handleError(int $number, string $message, string $file, int $line, array $context)
	{
		$data = [
			"result" => "error",
			"type" => "error",
			"code" => 500,
			"message" => $message
		];
		
		if ($this->showTrace) {
			$data["trace"] = array_map(function($item) {
				return [
					"file" => isset($item["file"]) ? $item["file"] : null,
					"line" => isset($item["line"]) ? $item["line"] : null,
					"function" => isset($item["function"]) ? $item["function"] : null,
					"class" => isset($item["class"]) ? $item["class"] : null,
					"type" => isset($item["type"]) ? $item["type"] : null
				];
			}, debug_backtrace());
		}
		
		$this->handle($data);
	}
	
	/**
	 * Handle an exception 
	 * 
	 * @param \Throwable $exception
	 */
	public function handleException(\Throwable $exception)
	{
		$code = $exception->getCode() ?: 500;
		$data = [
			"result" => "error",
			"type" => "exception",
			"code" => is_numeric($code) ? $code : 500,
			"message" => $exception->getMessage()
		];
		
		if ($this->showTrace) {
			$data["trace"] = array_map(function($trace) { 
				unset($trace["args"]);
				
				return $trace;
			}, $exception->getTrace());
		}
		
		$this->handle($data);
	}
	
	private function handle(array $responseData)
	{
		$format = $this->request->format();
		$body = $format->parse($responseData);
		$code = isset($responseData["code"]) ? $responseData["code"] : 500;
		
		$this->response->header("Content-Type: ". $format->mimeType());
		$this->response->code($code);
		$this->response->body($body);
		$this->response->output();
	}
}