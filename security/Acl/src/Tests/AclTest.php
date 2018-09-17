<?php
namespace Acl\Tests;

use Acl\Acl;

class AclTest extends \PHPUnit_Framework_TestCase
{
	const RESOURCE_NAME = "foo";
	
	public function testClean()
	{
		$acl = new Acl();
		
		$this->assertFalse($acl->isAllowed(self::RESOURCE_NAME));
	}
	
	public function testAllowAll()
	{
		$acl = new Acl();
		$acl->allowByDefault(true);
		
		$this->assertTrue($acl->isAllowed(self::RESOURCE_NAME));
		$this->assertTrue($acl->isAllowed("SomethingElse"));
	}
	
	public function testReadResource()
	{
		$acl = new Acl();
		$acl->allow(self::RESOURCE_NAME, "read", ["id" => 1]);
		
		$this->assertTrue($acl->isAllowed(self::RESOURCE_NAME, "read", ["id" => 1]));
		$this->assertFalse($acl->isAllowed(self::RESOURCE_NAME, "read", ["id" => 2]));
	}
	
	public function testReadAllOfResource()
	{
		$acl = new Acl();
		$acl->allow(self::RESOURCE_NAME, "read");
		
		$this->assertTrue($acl->isAllowed(self::RESOURCE_NAME, "read", ["id" => 1]));
		$this->assertTrue($acl->isAllowed(self::RESOURCE_NAME, "read", ["id" => 2]));
	}
	
	public function testAllowEverythingOnResource()
	{
		$acl = new Acl();
		$acl->allow(self::RESOURCE_NAME);
		
		$this->assertTrue($acl->isAllowed(self::RESOURCE_NAME, "read", ["id" => 1]));
		$this->assertTrue($acl->isAllowed(self::RESOURCE_NAME, "create"));
	}
	
	public function testDenySingleAction()
	{
		$acl = new Acl();
		$acl->allow(self::RESOURCE_NAME);
		$acl->deny(self::RESOURCE_NAME, "delete");
		
		$this->assertFalse($acl->isAllowed(self::RESOURCE_NAME, "delete"));
		$this->assertTrue($acl->isAllowed(self::RESOURCE_NAME, "update"));
	}
	
	public function testDenySingleResource()
	{
		$acl = new Acl();
		$acl->allow(self::RESOURCE_NAME);
		$acl->deny(self::RESOURCE_NAME, null, ["id" => 1]);
		$acl->allow(self::RESOURCE_NAME, "read", ["id" => 2]);
		$acl->allow("blah");
		
		$this->assertFalse($acl->isAllowed(self::RESOURCE_NAME, "read", ["id" => 1]));
		$this->assertTrue($acl->isAllowed(self::RESOURCE_NAME, "read", ["id" => 2]));
	}
	
	public function testMerge()
	{
		$a = new Acl();
		$b = new Acl();
		
		$a->allow("my-resource", "read");
		$b->allow("my-resource", "write");
		
		$this->assertFalse($a->isAllowed("my-resource", "write"));
		
		$a->merge($b);
		
		$this->assertTrue($a->isAllowed("my-resource", "write"));
	}
}