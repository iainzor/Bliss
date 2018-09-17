<?php
namespace Acl\Permission;

interface PermissionInterface
{
	public function resourceName($name = null);
	
	public function action($action = null);
	
	public function params(array $params = null);
	
	public function matches(array $params);
	
	public function isAllowed($flag = null);
}