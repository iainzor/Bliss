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
		$found = [];
		foreach ($this->routes as $def) {
			if ($def->isMatch($path)) {
				$found[] = $def;
			}
		}
		
		usort($found, function($a, $b) {
			if ($a->getWeight() === $b->getWeight()) {
				return 0;
			}
			return $a->getWeight() > $b->getWeight() ? -1 : 1; 
		});
		
		if (empty($found)) {
			throw new RouteNotFoundException("Could not find a route matching '{$path}'");
		}
		
		$routeDef = array_shift($found);
		
		return $routeDef->generateRoute($path);
	}
}