<?php
namespace Acl\Permission;

class RegexPermission extends AbstractPermission
{
	/**
	 * Check if the regex permission matches the supplied path
	 * 
	 * @param string $path
	 * @return boolean
	 */
	public function matches($path) 
	{
		return preg_match("/". $this->path ."/i", $path);
	}
}