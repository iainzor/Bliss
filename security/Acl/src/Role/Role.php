<?php
namespace Acl\Role;

use Acl\Acl;

class Role extends Acl implements RoleInterface
{
	/**
	 * @var string
	 */
	protected $name;
	
	/**
	 * Constructor
	 * 
	 * @param string $name An optional name for the role
	 */
	public function __construct($name = null)
	{
		$this->name($name);
	}
	
	/**
	 * Get or set the name of the role
	 * 
	 * @param string $name
	 */
	public function name($name = null)
	{
		if ($name !== null) {
			$this->name = $name;
		}
		return $this->name;
	}
	
	/**
	 * Override the default merge method to change the role's name
	 * 
	 * @param \Acl\AclInterface $acl
	 */
	public function merge(\Acl\AclInterface $acl) {
		parent::merge($acl);
		
		if ($acl instanceof Role) {
			$this->name = $acl->name();
		}
	}
}