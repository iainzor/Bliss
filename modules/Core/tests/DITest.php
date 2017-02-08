<?php
use PHPUnit\Framework\TestCase,
	Core\DI;

class DITest extends TestCase
{
	public function testEmptyInjector()
	{
		$di = new DI();
		$a = $di->get(DateTime::class);
		$b = $di->get(DateTime::class);
		
		$this->assertNotEquals($a, $b);
	}
	
	public function testBasicFunctionality()
	{
		$di = new DI();
		$di->register(new DateTime("1970-01-01", new DateTimeZone("UTC")));
		
		$epoch = $di->get(DateTime::class);
		$date = $epoch->format("Y-m-d");
		$this->assertEquals("1970-01-01", $date);
	}
}