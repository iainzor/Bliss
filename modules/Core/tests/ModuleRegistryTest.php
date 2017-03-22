<?php
use PHPUnit\Framework\TestCase;

require __DIR__ ."/mock/MockApplication.php";

class ModuleRegistryTest extends TestCase
{
	public function testCrossDependency()
	{
		$app = new MockApplication();
		$registry = new Core\ModuleRegistry($app);
		$registry->registerAll(__DIR__ ."/mock/modules");
		
		$moduleA = $registry->module("ModuleA");
		$moduleB = $registry->module("ModuleB");
		
		$this->assertInstanceOf(\ModuleA\Module::class, $moduleA->instance($app));
		$this->assertInstanceOf(\ModuleB\Module::class, $moduleB->instance($app));
	}
}