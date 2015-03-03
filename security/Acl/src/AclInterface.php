<?php
namespace Acl;

interface AclInterface 
{
	public function allowByDefault($flag = null);
	
	public function allow($resourceName, $action = null, array $params = []);
	
	public function deny($resourceName, $action = null, array $params = []);
	
	public function isAllowed($resourceName, $action = null, array $params = []);
	
	public function assertIsAllowed($resourceName, $action = null, array $params = []);
	
	public function merge(AclInterface $acl);
	
	public function permissions();
}