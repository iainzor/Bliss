<?php
namespace Docs\Controller;

class ApiController extends \Bliss\Controller\AbstractController
{
	public function reflectAction()
	{
		$request = $this->app->request();
		$path = $request->param("path");
		$parts = explode("-", $path);
		$className = implode("\\", $parts);
		$reflection = new \ReflectionClass($className);
		
		
		
		$properties = [];
		foreach ($reflection->getProperties() as $prop) {
			$properties[] = [
				"name" => $prop->getName(),
				"comment" => $prop->getDocComment()
			];
		}
		
		return [
			"name" => $reflection->getName(),
			"namespace" => $reflection->getNamespaceName(),
			"properties" => $properties,
			"implements" => $reflection->getInterfaceNames()
		];
	}
}