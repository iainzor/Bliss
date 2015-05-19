<?php
namespace Database\Tests;

use Database\Query,
	Database\SelectQuery;

class QueryTest extends \PHPUnit_Framework_TestCase
{
	public function testQuery() 
	{
		$query = new Query();
		$query->selectFrom("A", "id, foo")
			  ->leftJoin("B ON A.baz = B.baz", [
				  "bazBlah" => "fooBar"
			  ])
			  ->where("`foo` = 'bar'");
		$sql = $query->generateSelectSql();
		
		
		
		$this->assertEquals(
			"SELECT `A`.`id`, `A`.`foo`, `B`.`bazBlah` AS `fooBar` " . 
			"FROM `my_table` " .
			"JOIN `B` ON `A`.`baz` = `B`.`baz` " .
			"WHERE `A`.`foo` = 'bar'", 
		$sql);
	}
	
	public function _testSelectQuery()
	{
		$query = new SelectQuery("my_table");
		$sql = $query->generateSql();
		
		$this->assertEquals("SELECT * FROM `my_table`", $sql);
	}
}