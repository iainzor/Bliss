<?php
use PHPUnit\Framework\TestCase,
	Database\Registry;

class RegistryTest extends TestCase
{
	public function testEmptyRegistry()
	{
		$registry = new Registry();
		
		$this->assertTrue($registry->isEmpty());
	}
	
	public function testRegister()
	{
		$registry = new Registry();
		$registry->set("default", new \PDO("sqlite::memory:"));
		
		$pdo = $registry->get("default");
		$this->assertInstanceOf(\PDO::class, $pdo);
	}
}