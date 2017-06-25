<?php
use PHPUnit\Framework\TestCase,
	Http\Router;

class RequestTest extends TestCase
{
	public function testBasicUri()
	{
		$uri = "path/to/my-resource";
		$router = new Router();
		$router->when("/^path\/to\/my-resource$/i")
				->module("my-module")
				->controller("my-controller")
				->action("my-resource");
		
		$route = $router->find($uri);
		
		$this->assertEquals("my-module", $route->module());
		$this->assertEquals("my-controller", $route->controller());
		$this->assertEquals("my-resource", $route->action());
	}
	
	public function testComplexUri()
	{
		$uri = "users/123/profile";
		$router = new Router();
		$router->when("/^users\/([0-9]+)\/?([a-z0-9-]+)?$/")
				->module("users")
				->controller("user")
				->action(function(array $matches) { return $matches[2]; })
				->params(function(array $matches) {
					return [
						"userId" => $matches[1]
					];
				});
				
		$route = $router->find($uri);
		
		$this->assertEquals(123, $route->param("userId"));
		$this->assertEquals("profile", $route->action());
	}
	
	public function testWeight()
	{
		$router = new Router();
		
		$router->when("/^foo\/bar\.([a-z0-9]+)$/i")
			->module("foo")
			->controller("bar")
			->params([
				1 => "format"
			])
			->weight(100);
		
		$router->when("/^foo\/([a-z0-9-]+)\.([a-z0-9]+)$/i")
			->module("foo")
			->controller("baz")
			->params([
				1 => "action",
				2 => "format"
			]);
		
		$routeA = $router->find("foo/bar.json");
		$routeB = $router->find("foo/baz.json");
		
		$this->assertNotEquals($routeA->controller(), $routeB->controller());
		
		
		/*
		$routerA = new Router();
		$routerA->when("/^foo\/([a-z0-9]+)\.([a-z]+)$/i")->module("foo")->params([1 => "controller", 2 => "format"]);
		$routerA->when("/^foo\/bar$/i")->module("foo")->controller("bar");
		$routeA = $routerA->find("foo/baz.png");
		
		$this->assertEquals("baz", $routeA->controller());
		
		$routerB = new Router();
		$routerB->when("/^foo\/([a-z0-9]+)\.([a-z]+)$/i")->module("foo")->params([1 => "controller", 2 => "format"]);
		$routerB->when("/^foo\/bar$/i")->module("foo")->controller("bar")->weight(100);
		
		$routeB = $routerB->find("foo/bar.json");
		
		$this->assertEquals("bar", $routeB->controller());
		*/
	}
}
