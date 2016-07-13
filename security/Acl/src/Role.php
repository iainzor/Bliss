<?php
namespace Acl;

class Role extends Acl
{
	/**
	 * @var int
	 */
	protected $id;
	
	/**
	 * @var string
	 */
	protected $name;
	
	/**
	 * @var int[]
	 */
	protected $inherits = [];
	
	/**
	 * @var RoleRegistry
	 */
	private $registry;
	
	/**
	 * Get or set the role's parent registry
	 * 
	 * @param RoleRegistry $registry
	 * @return RoleRegistry
	 */
	public function registry(RoleRegistry $registry = null)
	{
		if ($registry !== null) {
			$this->registry = $registry;
		}
		return $this->registry;
	}
	
	/**
	 * Get or set the unique ID of the role
	 * 
	 * @param int $id
	 * @return int
	 */
	public function id($id = null)
	{
		return $this->getSet("id", $id, self::VALUE_INT);
	}
	
	/**
	 * Get or set the role's name
	 * 
	 * @param string $name
	 * @return string
	 */
	public function name($name = null)
	{
		return $this->getSet("name", $name);
	}
	
	/**
	 * Get or set the IDs of the roles this role should inherit permissions from
	 * 
	 * @param int[] $roleIds
	 * @return int[]
	 */
	public function inherits(array $roleIds = null)
	{
		return $this->getSet("inherits", $roleIds);
	}
	
	/**
	 * Overrides the default isAllowed method in order to check inherited roles.
	 * 
	 * @param string $path
	 * @param string $action
	 * @return boolean
	 */
	public function isAllowed($path, $action) 
	{
		$isAllowed = parent::isAllowed($path, $action);
		
		if (!$isAllowed) {
			foreach ($this->inherits as $roleId) {
				if ($roleId !== $this->id) {
					$childRole = $this->registry()->role($roleId);
					$childIsAllowed = $childRole->isAllowed($path, $action);
					
					if ($childIsAllowed === true) {
						$isAllowed = true;
					}
				}
			}
		}
		
		return $isAllowed;
	}
}