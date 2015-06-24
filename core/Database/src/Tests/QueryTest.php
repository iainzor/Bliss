<?php
namespace Database\Tests;

use Database\Query;

class QueryTest extends \PHPUnit_Framework_TestCase
{
	public function testQuery() 
	{
		$pdo = new \Database\PDO("sqlite::memory:");
		$query = new Query\SelectQuery($pdo);
		$query->from("my_table", ["*"]);
		$query->where("foo = :foo");
		$query->where("bar = :bar");
		$query->leftJoin("B", "A.baz = B.baz", [
				"B.bazBlah AS fooBar"
		]);
		$query->orderBy("A.id DESC");
		
		$sql = $query->sql();
		
		$this->assertEquals(
			"SELECT `my_table`.*, `B`.`bazBlah` AS `fooBar` " . 
			"FROM `my_table` " .
			"LEFT JOIN `B` ON A.baz = B.baz " .
			"WHERE foo = :foo AND bar = :bar " .
			"ORDER BY A.id DESC", 
		$sql);
	}
}