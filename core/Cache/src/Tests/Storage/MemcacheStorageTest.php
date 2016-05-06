<?php
namespace Cache\Tests\Storage;

use Cache\Driver\Memcache\Storage as MemcacheStorage,
	Cache\Registry,
	Cache\Resource\Resource;

class MemcacheStorageTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @var MemcacheStorage
	 */
	private $storage;
	
	public function setUp()
	{
		$memcache = new \Memcache();
		$memcache->addServer("127.0.0.1");
		
		$this->storage = new MemcacheStorage($memcache);
	}
	
	public function testIO()
	{
		$registry = new Registry($this->storage);
		
		$falseResource = $this->storage->get("foo");
		
		$this->assertFalse($falseResource);
		
		$resource = new Resource($registry, [
			"contents" => "bar"
		]);
		$this->storage->put("foo", $resource);
		
		$resourceContents = $this->storage->get("foo");
		
		$this->assertEquals("bar", $resourceContents);
		
		$this->storage->delete("foo");
	}
}