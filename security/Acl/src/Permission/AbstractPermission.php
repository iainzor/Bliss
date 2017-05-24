<?php
namespace Acl\Permission;

use Acl\Action;

abstract class AbstractPermission implements PermissionInterface 
{
	/**
	 * @var string
	 */
	protected $path;
	
	/**
	 * @var Action[]
	 */
	protected $actions;
	
	/**
	 * @var int
	 */
	protected $priority = 1;
	
	/**
	 * Constructor
	 * 
	 * @param string $path
	 * @param array $actions
	 * @param int $priority
	 */
	public function __construct($path, array $actions = [], $priority = 1)
	{
		$this->path($path);
		$this->actions($actions);
		$this->priority($priority);
	}
	
	/**
	 * Get or set the permission path
	 * 
	 * @param string $path
	 * @return string
	 */
	public function path($path = null)
	{
		if ($path !== null) {
			$this->path = $path;
		}
		return $this->path;
	}
	
	/**
	 * Get or set which actions are or are not allowed for the permission.
	 * 
	 * The actions array can either be an array of \\Acl\\Action instances or an array where
	 * the keys are the action names and the value is a boolean value of whether the action is allowed.
	 * 
	 * @param array $actions
	 * @return Action[]
	 */
	public function actions(array $actions = null)
	{
		if ($actions !== null) {
			$this->actions = [];
			foreach ($actions as $actionName => $isAllowed) {
				if ($isAllowed instanceof Action) {
					$action = $isAllowed;
				} else {
					$action = new Action($actionName, $isAllowed);
				}
				
				$this->actions[$action->name()] = $action;
			}
		}
		return $this->actions;
	}
	
	/**
	 * Get or set the priority of the permission.  The higher the priority the 
	 * more chance of the permission being used.
	 *  
	 * @param int $priority
	 * @return int
	 */
	public function priority($priority = null)
	{
		if ($priority !== null) {
			$this->priority = (int) $priority;
		}
		return $this->priority;
	}
	
	/**
	 * Check if an action is allowed 
	 * 
	 * @param string $action
	 * @return boolean
	 */
	public function isAllowed($action)
	{
		return isset($this->actions[$action])
			? $this->actions[$action]->isAllowed()
			: false;
	}
	
	public function toArray() 
	{
		$actions = [];
		foreach ($this->actions() as $action) {
			$actions[$action->name()] = $action->isAllowed();
		}
		
		$className = get_class($this);
		$parts = explode("\\", $className);
		$type = array_pop($parts);
		
		return [
			"path" => $this->path(),
			"type" => $type,
			"priority" => $this->priority(),
			"actions" => $actions
		];
	}
}