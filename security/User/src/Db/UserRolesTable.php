<?php
namespace User\Db;

use Database\Table\AbstractTable,
	Database\Model,
	User\Role;

class UserRolesTable extends AbstractTable implements Model\ModelGeneratorInterface
{
	use Model\ModelGeneratorTrait;
	
	const NAME = "user_roles";
	
	public function defaultName() { return self::NAME; }
	
	public function createModelInstance() { return new Role(); }
}