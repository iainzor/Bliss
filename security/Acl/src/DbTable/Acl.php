<?php
namespace Acl\DbTable;

use Database\Table\AbstractTable,
	Database\Table\ColumnExpr,
	Database\Query\SelectQuery;

class Acl extends AbstractTable
{
	const NAME = "acl";
	
	public function defaultName() { return self::NAME; }
	
	public static function joinOnto(SelectQuery $query, $role, $resourceName, $resourceIdColumn = "id")
	{
		$db = $query->db();
		$table = new self($query->db());
		$tableName = $table->fullName(true);
		$localTable = $query->table();
		$localTableName = $localTable->fullName(true);
		$role = $db->quote($role);
		$resourceName = $db->quote($resourceName);
		
		$query->where([
			"(SELECT isAllowed FROM {$tableName} WHERE `role` = {$role} AND `action` = 'read' AND `resourceName` = {$resourceName} AND `resourceId` IS NULL) = 1",
			"(SELECT isAllowed FROM {$tableName} WHERE `role` = {$role} AND `action` = 'read' AND `resourceName` = {$resourceName} AND `resourceId` = {$localTableName}.`{$resourceIdColumn}`) = 1",
			"(SELECT COUNT(*) FROM {$tableName} WHERE `role` = {$role} AND `action` = 'read' AND `resourceName` = {$resourceName}) = 0"
		], SelectQuery::COMPARE_OR);
		$query->andWhere([
			"(SELECT COUNT(*) FROM {$tableName} WHERE `role` = {$role} AND `action` = 'read' AND `resourceName` = {$resourceName} AND `resourceId` = {$localTableName}.`{$resourceIdColumn}` AND `isAllowed` = 0) = 0"
		]);
	}
}