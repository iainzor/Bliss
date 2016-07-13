<?php
namespace User;

use Database\Model\ModelInterface,
	Acl\Role as BaseRole;

class Role extends BaseRole implements ModelInterface
{
	const BASE_ROLE = -1;
	const GUEST_ROLE = 0;
	const USER_ROLE = 1;
	const ADMIN_ROLE = 2;
	
	/**
	 * @var int
	 */
	protected $id = self::BASE_ROLE;
}