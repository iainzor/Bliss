<?php
namespace Acl\Permission;

interface PermissionInterface
{
	/**
	 * @param string $path
	 * @return boolean
	 */
	public function matches($path);
	
	/**
	 * @param string $action
	 * @return boolean
	 */
	public function isAllowed($action);
}