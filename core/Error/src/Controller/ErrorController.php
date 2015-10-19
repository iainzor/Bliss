<?php
namespace Error\Controller;

class ErrorController extends \Bliss\Controller\AbstractController
{
	public function handleAction(\Error\Module $errorModule)
	{
		$request = $this->app->request();
		$response = $this->app->response();
		$e = $request->param("exception");
		
		if ($e) {
			$message = substr(preg_replace("/\n/", " ", $e->getMessage()), 0, 50);
			$response->header("X-Exception: {$message}");
		};
		
		$data = [
			"result" => "error",
			"message" => isset($e) ? $e->getMessage() : "Unknown",
			"code" => isset($e) ? $e->getCode() : 500
		];
		
		if ($errorModule->showConsole()) {
			$data["logs"] = $this->app->logs();
		}
		
		if ($errorModule->showTrace()) {
			$data["trace"] = isset($e) ? $e->getTrace() : [];
			$data["traceString"] = isset($e) ? $e->getTraceAsString() : null;
		}
		
		return $data;
	}
}