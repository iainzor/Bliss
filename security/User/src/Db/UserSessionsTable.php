<?php
namespace User\Db;

use Database\Table\AbstractTable;

class UserSessionsTable extends AbstractTable
{
	const NAME = "user_sessions";
	
	public function defaultName() { return self::NAME; }
}