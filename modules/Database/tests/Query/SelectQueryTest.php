<?php
namespace Query;

use PHPUnit\Framework\TestCase,
	Database\Query\SelectQuery,
	Database\Column;

class SelectQueryTest extends TestCase
{
	public function testBasicSelect()
	{
		$query = new SelectQuery([
			"my_column" => "alias_of_my_column",
			"my_other_column",
			new Column\ColumnExpr("COUNT(*) AS total")
		]);
		$query->from("my_table");
		
		$this->assertCount(3, $query->columns());
		$this->assertInstanceOf(\Database\Table\TableInterface::class, $query->table());
	}
}