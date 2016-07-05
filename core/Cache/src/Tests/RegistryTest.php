<?php
namespace Cache\Tests;

use Cache\Registry,
	Cache\Resource\Resource;

class RegistryTest extends \PHPUnit_Framework_TestCase
{
	public function testInitialConditions()
	{
		$storage = new MockStorage();
		$registry = new Registry($storage);
		$cache = $registry->get("my-resource", 123);
		
		$this->assertFalse($cache);
	}
	
	public function testPut()
	{
		$storage = new MockStorage();
		$registry = new Registry($storage);
		$resource = new Resource($registry);
		$resource->resourceName("my-resource");
		$resource->resourceId(123);
		$resource->contents("hello");
		$registry->put($resource);
		
		$this->assertEquals("hello", $registry->get("my-resource", 123)->contents());
	}
	
	public function testStorage()
	{
		$storage = new MockStorage();
		$registry = new Registry($storage);
		$resource = new Resource($registry);
		$resource->resourceName("my-resource");
		$resource->resourceId(123);
		$resource->contents("hello");
		
		$registry->put($resource);
		
		$registryB = new Registry($storage);
		$cacheB = $registryB->get("my-resource", 123);
		
		$this->assertEquals($resource->contents(), $cacheB->contents());
	}
	
	public function testDeleteChildren()
	{
		$storage = new MockStorage();
		$registry = new Registry($storage);
		
		$parent = $registry->create("my-resource", 123);
		$child = $registry->create("my-resource", 123, ["foo" => "bar"]);
		
		$registry->put($parent);
		$registry->put($child);
		
		$this->assertTrue($registry->exists("my-resource", 123));
		$this->assertTrue($registry->exists("my-resource", 123, ["foo" => "bar"]));
		
		$registry->delete($parent);
		
		$this->assertFalse($registry->exists("my-resource", 123, ["foo" => "bar"]));
	}
}