<?php
namespace Database\Tests;

use Database\Query;

class QueryTest extends \PHPUnit_Framework_TestCase
{
	public function testQuery() 
	{
		$query = new Query\SelectQuery("A", ["id","foo"]);
		$query->leftJoin("B", "A.baz = B.baz", [
				"B.bazBlah AS fooBar"
			])
			->where("`foo` = 'bar'")
			->orderBy("A.id DESC");
		
		$sql = $query->sql();
		
		$this->assertEquals(
			"SELECT `A`.`id`, `A`.`foo`, `B`.`bazBlah` AS `fooBar` " . 
			"FROM `A` " .
			"LEFT JOIN `B` ON A.baz = B.baz " .
			"WHERE `foo` = 'bar' " .
			"ORDER BY A.id DESC", 
		$sql);
	}
}