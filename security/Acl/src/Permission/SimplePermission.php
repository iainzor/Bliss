<?php
namespace Acl\Permission;

class SimplePermission extends AbstractPermission
{
	/**
	 * Check if the supplied path is an exact match of the permission's path
	 * 
	 * @param string $path
	 * @return boolean
	 */
	public function matches($path) 
	{
		return strtolower($this->path) === strtolower($path);
	}
}