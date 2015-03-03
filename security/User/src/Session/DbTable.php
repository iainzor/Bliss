<?php
namespace User\Session;

use Database\Table\AbstractTable;

class DbTable extends AbstractTable
{
	public function defaultName() { return "user_sessions"; }
}