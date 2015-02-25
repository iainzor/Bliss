<?php
namespace Error\Controller;

class ErrorController extends \Bliss\Controller\AbstractController
{
	public function handleAction()
	{
		$request = $this->app->request();
		$response = $this->app->response();
		$e = $request->param("exception");
		
		if ($e) {
			$response->header("X-Exception: {$e->getMessage()}");
		};
		
		return [
			"result" => "error",
			"message" => isset($e) ? $e->getMessage() : "Unknown",
			"code" => isset($e) ? $e->getCode() : 0,
			"trace" => isset($e) ? $e->getTrace() : [],
			"traceString" => isset($e) ? $e->getTraceAsString() : null,
			"logs" => $this->app->logs()
		];
	}
}