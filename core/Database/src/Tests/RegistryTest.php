<?php
namespace Database\Tests;

use Database\Registry;

class RegistryTest extends \PHPUnit_Framework_TestCase
{
	public function tearDown() 
	{
		unlink(__DIR__ ."/test.db");
	}
	
	public function testAddServer()
	{
		$registry = new Registry();
		$registry->addServer("sqlite:". __DIR__ ."/test.db");
		
		$this->assertEquals(1, $registry->totalServers());
		$this->assertInstanceOf("\PDO", $registry->generateConnection());
	}
}