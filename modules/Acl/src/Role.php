<?php
namespace Acl;

class Role
{
	/**
	 * @var int
	 */
	public $id;
	
	/**
	 *
	 * @var PermissionInterface[]
	 */
	public $permissions = [];
	
	/**
	 * Constructor
	 * 
	 * @param int $id
	 */
	public function __construct(int $id)
	{
		$this->id = $id;
	}
}