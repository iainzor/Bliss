<?php
namespace Error\Controller;

class ErrorController extends \Bliss\Controller\AbstractController
{
	public function handleAction()
	{
		$request = $this->app->request();
		$e = $request->param("exception");
		
		return [
			"result" => "error",
			"message" => isset($e) ? $e->getMessage() : "Unknown",
			"code" => isset($e) ? $e->getCode() : 0,
			"trace" => isset($e) ? $e->getTrace() : [],
			"logs" => $this->app->logs()
		];
	}
}