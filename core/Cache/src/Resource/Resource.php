<?php
namespace Cache\Resource;

use Bliss\Component,
	Cache\Registry;

class Resource extends Component implements ResourceInterface 
{
	/**
	 * @var Registry
	 */
	private $registry;
	
	/**
	 * @var string
	 */
	protected $resourceName;
	
	/**
	 * var string
	 */
	protected $resourceId;
	
	/**
	 * @var array
	 */
	protected $params = [];
	
	/**
	 * @var string
	 */
	protected $contents;
	
	/**
	 * @var int
	 */
	protected $expires = null;
	
	/**
	 * @var boolean
	 */
	protected $isExpired = true;
	
	/**
	 * Constructor
	 * 
	 * @param Registry $registry
	 * @param array $params
	 */
	public function __construct(Registry $registry, array $params = []) 
	{
		$this->registry = $registry;
		
		self::populate($this, $params);
	}
	
	/**
	 * Get or set the cache's resource name
	 * 
	 * @param string $resourceName
	 * @return string
	 */
	public function resourceName($resourceName = null) 
	{
		if ($resourceName !== null) {
			$this->resourceName = $resourceName;
		}
		
		return $this->resourceName;
	}
	
	/**
	 * Get or set the cache's unique resource ID
	 * 
	 * @param string $resourceId
	 * @return string
	 */
	public function resourceId($resourceId = null) 
	{
		if ($resourceId !== null) {
			$this->resourceId = $resourceId;
		}
		
		return $this->resourceId;
	}
	
	/**
	 * Get or set the parameters for the cache
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
	 * Put the contents of the cache
	 * 
	 * @param string $contents
	 */
	public function contents($contents = null) 
	{
		if ($contents !== null) {
			$this->contents = $contents;
		}
		
		return $this->contents;
	}
	
	/**
	 * Get or set the lifetime, in seconds, of the resource.
	 * Defaults to 30 seconds
	 * 
	 * @param int $expires
	 * @return int
	 */
	public function expires($expires = null)
	{
		if ($expires !== null) {
			$this->expires = $expires;
		}
		if ($this->expires === null) {
			$this->expires = 30;
		}
		return $this->expires;
	}
	
	/**
	 * Check if the cache resource is expired
	 * 
	 * @return boolean
	 */
	public function isExpired()
	{
		return $this->isExpired;
	}
	
	/**
	 * Save the cache resource to the registry
	 * 
	 * @return boolean
	 */
	public function save()
	{
		$this->isExpired = false;
		
		return $this->registry->put($this);
	}
	
	/**
	 * Attempt to load the cache resource and return its contents
	 * 
	 * @return mixed
	 */
	public function load()
	{
		$resource = $this->registry->get($this->resourceName, $this->resourceId, $this->params);
		if ($resource) {
			$this->contents($resource->contents());
			return $resource->contents();
		} else {
			$this->isExpired = true;
			return false;
		}
	}
}