<?php
namespace Http;

class Router
{
	/**
	 * @var RouteDefinition[]
	 */
	private $routes = [];
	
	/**
	 * Create an return a new route
	 * 
	 * @param string $test A regex string used to match paths
	 * @return \Http\Route
	 */
	public function when(string $test) : RouteDefinition
	{
		$this->routes[] = $route = new RouteDefinition($test);
		return $route;
	}
	
	/**
	 * Attempt to find a route matching the provided path
	 * 
	 * @param string $path
	 * @return \Http\Route
	 * @throws \Exception
	 */
	public function find(string $path) : Route
	{
		$routeDef = null;
		foreach ($this->routes as $def) {
			if ($def->isMatch($path)) {
				$routeDef = $def;
			}
		}
		
		if ($routeDef === null) {
			throw new \Exception("Could not find a route matching '{$path}'");
		}
		
		return $routeDef->generateRoute($path);
	}
}