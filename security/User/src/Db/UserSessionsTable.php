<?php
namespace User\Db;

use Database\Table\AbstractTable,
	Database\Model,
	User\Session\Session;

class UserSessionsTable extends AbstractTable implements Model\ModelGeneratorInterface
{
	use Model\ModelGeneratorTrait;
	
	const NAME = "user_sessions";
	
	public function defaultName() { return self::NAME; }
	
	public function createModelInstance() { return new Session(); }
}