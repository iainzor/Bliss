<?php
use PHPUnit\Framework\TestCase,
	Http\Application;

class ApplicationTest extends TestCase
{
	public function testApplication()
	{
		$app = new Application();
		$app->moduleRegistry()->registerDirectory(__DIR__ . DIRECTORY_SEPARATOR . "MockModule");
		$app->start();
		
		$route = $app->router()->find("users/123/profile");
		
		$moduleName = $route->module();
		$moduleDef = $app->moduleRegistry()->module($moduleName);
		$moduleInstance = $moduleDef->instance($app);
		
		$controllerName = $route->controller();
		$controllerDef = $moduleDef->controller($controllerName);
		$controllerInstance = $controllerDef->instance($app);
		
		$this->assertInstanceOf(\MockModule\Module::class, $moduleInstance);
		$this->assertInstanceOf(\MockModule\Controller\User::class, $controllerInstance);
		$this->assertEquals(123, $route->param("userId"));
	}
}