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
	
	public function handleError(int $number, string $message, string $file, int $line, array $context)
	{
		$this->handle([
			"result" => "error",
			"message" => $message
		]);
	}
	
	public function handleException(\Throwable $exception)
	{
		$this->handle([
			"result" => "error",
			"message" => $exception->getMessage()
		]);
	}
	
	private function handle(array $responseData)
	{
		$format = $this->request->format();
		$body = $format->parse($responseData);
		
		$this->response->header("Content-Type: ". $format->mimeType());
		$this->response->code(500);
		$this->response->body($body);
		$this->response->output();
	}
}