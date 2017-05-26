<?php
namespace Acl;

interface RoleProviderInterface
{
	public function registerAclRoles(Acl $acl);
}