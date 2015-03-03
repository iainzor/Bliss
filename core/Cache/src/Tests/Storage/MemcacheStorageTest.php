<?php
namespace Cache\Tests\Storage;

use Cache\Storage\MemcacheStorage,
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
		$resource = $this->storage->get("foo");
		
		$this->assertFalse($resource);
		
		$this->storage->put("foo", "bar");
		
		$resource = $this->storage->get("foo");
		
		$this->assertEquals("bar", $resource);
		
		$this->storage->delete("foo");
	}
}