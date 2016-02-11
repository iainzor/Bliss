<?php
namespace User\Db;

use Database\Table\AbstractTable;

class UserSettingsTable extends AbstractTable 
{
	const NAME = "user_settings";
	
	public function defaultName() { return self::NAME; }
}