<?php
namespace Acl\Role;

use Acl\AclInterface;

interface RoleInterface extends AclInterface
{
	public function name($name = null);
}