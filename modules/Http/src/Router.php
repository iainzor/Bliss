<?php
namespace Http;

class Router
{
	/**
	 * @var RouteDefinition[]
	 */
	private $routes = [];
	
	/**
	 * Initialize the router
	 * Loops through all registered modules and collects routes from route providers
	 * 
	 * @param \Http\Application $app
	 */
	public function init(Application $app)
	{
		$app->moduleRegistry()->each(function(\Core\ModuleDefinition $moduleDef) use ($app) {
			$module = $moduleDef->instance($app);
			
			if ($module instanceof RouteProviderInterface) {
				$module->registerRoutes($this);
			}
		});
	}
	
	/**
	 * Create an return a new route
	 * 
	 * @param string $test A regex string used to match paths
	 * @return RouteDefinition
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
			throw new RouteNotFoundException("Could not find a route matching '{$path}'");
		}
		
		return $routeDef->generateRoute($path);
	}
}