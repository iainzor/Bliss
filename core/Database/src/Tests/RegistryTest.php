<?php
namespace Database\Tests;

use Database\Registry;

class RegistryTest extends \PHPUnit_Framework_TestCase
{
	public function testAddServer()
	{
		$registry = new Registry();
		$registry->addServer("sqlite::memory:");
		
		$this->assertEquals(1, $registry->totalServers());
		$this->assertInstanceOf("\PDO", $registry->generateConnection());
	}
}