<?php
use PHPUnit\Framework\TestCase,
	Http\Application;

class ApplicationTest extends TestCase
{
	public function testApplication()
	{
		$app = new Application();
		$app->moduleRegistry()->registerDirectory(__DIR__ . DIRECTORY_SEPARATOR . "MockModule");
		
		$route = $app->router()->find("users/123/profile");
		$caller = $app->routeCaller($route);
		$module = $caller->module();
		$controller = $caller->controller();
		
		$this->assertInstanceOf(\MockModule\Module::class, $module);
		$this->assertInstanceOf(\MockModule\Controller\User::class, $controller);
		$this->assertEquals(123, $route->param("userId"));
	}
}