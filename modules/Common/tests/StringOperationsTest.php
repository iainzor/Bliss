<?php
use PHPUnit\Framework\TestCase,
	Common\StringOperations;

class StingOperationsTest extends TestCase
{
	public function testCamelize()
	{
		$operations = new StringOperations();
		$test = "HelloWorld";
		$result = $operations->camelize("hello-world");
		
		$this->assertEquals($test, $result);
	}
}