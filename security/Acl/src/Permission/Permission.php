<?php
namespace Acl\Permission;

use Acl\Component;

class Permission extends Component implements PermissionInterface
{
	/**
	 * @var string
	 */
	protected $resourceName;
	
	/**
	 * @var string
	 */
	protected $action;
	
	/**
	 * @var array
	 */
	protected $params = [];
	
	/**
	 * @var boolean
	 */
	protected $isAllowed = false;
	
	/**
	 * Get or set the resource name the permission belongs to
	 * 
	 * @param string $name
	 * @return string
	 */
	public function resourceName($name = null) 
	{
		if ($name !== null) {
			$this->resourceName = $name;
		}
		return $this->resourceName;
	}
	
	/**
	 * Get or set the permission's action
	 * 
	 * @param string $action
	 * @return string
	 */
	public function action($action = null)
	{
		if ($action !== null) {
			$this->action = $action;
		}
		return $this->action;
	}
	
	/**
	 * Get or set the parameters of the permission
	 * 
	 * @param array $params
	 * @return array
	 */
	public function params(array $params = null) 
	{
		if ($params !== null) {
			$this->params = $params;
		}
		return $this->params;
	}
	
	/**
	 * Check if the permission's parameters match the provided values
	 * 
	 * @param array $params
	 * @return boolean
	 */
	public function matches(array $params)
	{
		$match = true;
		foreach ($this->params as $name => $value) {
			if (!isset($params[$name])) {
				$match = false;
			} else if ($params[$name] !== $value) {
				$match = false;
			}
		}
		return $match;
	}
	
	/**
	 * Get or set whether this permission is allowed
	 * 
	 * @param boolean $flag
	 */
	public function isAllowed($flag = null)
	{
		if ($flag !== null) {
			$this->isAllowed = (boolean) $flag;
		}
		return $this->isAllowed;
	}
	
	/**
	 * Generate a unqiue hash for a permission
	 * 
	 * @param string $resourceName
	 * @param int $resourceId
	 * @param string $action
	 * @return string
	 */
	public static function generateHash($resourceName, $resourceId = 0, $action = null)
	{
		return implode(".", [
			$resourceName,
			$resourceId ? (int) $resourceId : "*",
			$action ? $action : "*"
		]);
	}
}