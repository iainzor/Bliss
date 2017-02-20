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
}
