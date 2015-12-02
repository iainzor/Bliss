<?php
namespace Acl\Tests;

use Database\PDO,
	Database\Query,
	Acl\DbTable;

class DbQueryTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @var PDO
	 */
	private $db;
	
	public function setUp()
	{
		$this->db = new PDO("sqlite::memory:", null, null, [
			PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
		]);
		$this->db->driver("mysql");
		$this->db->schemaName("");
		$this->db->exec("
			CREATE TABLE `acl` (
				`id` INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
				`role` VARCHAR(64) NOT NULL,
				`resourceName` VARCHAR(64) NOT NULL,
				`resourceId` INTEGER NULL DEFAULT NULL,
				`action` VARCHAR(64) NOT NULL,
				`isAllowed` BOOLEAN NOT NULL DEFAULT 0
			);
		");
		$this->db->exec("
			INSERT INTO `acl` VALUES 
			(NULL, 'test', 'article', NULL, 'read', 1), 
			(NULL, 'test', 'article', 2, 'read', 0),
			
			(NULL, 'test', 'client', NULL, 'read', 1),
			(NULL, 'test', 'client', 1, 'read', 0),
			
			(NULL, 'user', 'client', NULL, 'read', 0),
			(NULL, 'user', 'client', 1, 'read', 1)
		");
		$this->db->exec("	
			CREATE TABLE `articles` (
				`id` INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
				`title` VARCHAR(128) NOT NULL
			);
		");
		$this->db->exec("INSERT INTO `articles` VALUES (NULL, 'Foo Bar'), (NULL, 'Bar Baz!');");
		$this->db->exec("	
			CREATE TABLE `clients` (
				`id` INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
				`name` VARCHAR(64) NOT NULL
			);
		");
		$this->db->exec("INSERT INTO `clients` VALUES (NULL, 'Foo Industries'), (NULL, 'Bar Corp'), (NULL, 'Baz Inc');");
	}
	
	public function testSetup()
	{
		$aclRecords = $this->db->fetchAll("SELECT * FROM `acl`");
		$articles = $this->db->fetchAll("SELECT * FROM `articles`");
		$clients = $this->db->fetchAll("SELECT * FROM `clients`");
		
		$this->assertCount(6, $aclRecords);
		$this->assertCount(2, $articles);
		$this->assertCount(3, $clients);
	}
	
	public function testAclJoin()
	{
		$query = new Query\SelectQuery($this->db);
		$query->from("articles");
		
		DbTable\Acl::joinOnto($query, "test", "article", "id");
		
		$results = $query->fetchAll();
		
		$this->assertCount(1, $results);
	}
	
	public function testAllowedAllButOne()
	{
		$query = new Query\SelectQuery($this->db);
		$query->from("clients");
		
		DbTable\Acl::joinOnto($query, "test", "client", "id");
		
		$results = $query->fetchAll();
		
		$this->assertCount(2, $results);
		$this->assertEquals("Bar Corp", $results[0]["name"]);
	}
	
	public function testDenyAllButOne()
	{
		$query = new Query\SelectQuery($this->db);
		$query->from("clients");
		
		DbTable\Acl::joinOnto($query, "user", "client", "id");
		
		$results = $query->fetchAll();
		
		$this->assertCount(1, $results);
	}
}