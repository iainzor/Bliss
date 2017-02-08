<?php
use PHPUnit\Framework\TestCase,
	Core\DI;

class DITest extends TestCase
{
	public function testEmptyInjector()
	{
		$di = new DI();
		$a = $di->create(DateTime::class);
		$b = $di->create(DateTime::class);
		
		$this->assertNotEquals($a, $b);
	}
}