<?php
namespace Bliss;

abstract class ResourceComponent extends Component implements Resource\ResourceInterface
{
	/**
	 * @var mixed
	 */
	protected $id;
	
	/**
	 * @var string
	 */
	protected $RESOURCE_NAME;
	
	/**
	 * @var int
	 */
	protected $created;
	
	/**
	 * @var int
	 */
	protected $updated;
	
	/**
	 * @return string
	 */
	abstract public function getResourceName();
	
	/**
	 * Get or set the resource component's ID
	 * 
	 * @param mixed $id
	 * @return mixed
	 */
	public function id($id = null)
	{
		if ($id !== null) {
			if (preg_match("/^[0-9]+$/", $id)) {
				$this->id = (int) $id;
			} else {
				$this->id = $id;
			}
		}
		return $this->id;
	}
	
	/**
	 * Get or set the resource ID
	 * The resource ID of a resource component is just it's own ID
	 * 
	 * @param int $resourceId
	 * @return int
	 */
	public function resourceId($resourceId = null) 
	{
		if ($resourceId !== null) {
			$this->id($resourceId);
		}
		return $this->id();
	}
	
	/**
	 * Get or set the resource's name
	 * 
	 * @param string $resourceName
	 * @return string
	 */
	public function resourceName($resourceName = null) 
	{
		if ($resourceName !== null) {
			$this->RESOURCE_NAME = $resourceName;
		} else if (!isset($this->RESOURCE_NAME)) {
			$this->RESOURCE_NAME = $this->getResourceName();
		}
		return $this->RESOURCE_NAME;
	}
	
	final public function resource_name() { return $this->resourceName(); }
	
	/**
	 * Get or set the UNIX timestamp of when the resource was created
	 * 
	 * @param int $created
	 * @return int
	 */
	public function created($created = null) 
	{
		if ($created !== null) {
			$this->created = (int) $created;
		} else if (!isset($this->created)) {
			$this->created = time();
		}
		return $this->created;
	}
	
	/**
	 * Get or set the UNIX timestamp of when the resource was last updated
	 * 
	 * @param int $updated
	 * @return int
	 */
	public function updated($updated = null) 
	{
		if ($updated !== null) {
			$this->updated = (int) $updated;
		} else if (!isset($this->updated)) {
			$this->updated = $this->created();
		}
		return $this->updated;
	}
	
	/**
	 * Override the default method to remove additional data
	 * 
	 * @return array
	 */
	public function toBasicArray()
	{
		$data = parent::toBasicArray();
		unset($data["RESOURCE_NAME"]);
		
		return $data;
	}
}