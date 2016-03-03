<?php
namespace Cache\Resource;

use Bliss\Component;

class Resource extends Component implements ResourceInterface 
{
	/**
	 * @var string
	 */
	protected $resourceName;
	
	/**
	 * var int
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
	 * @param int $resourceId
	 * @return int
	 */
	public function resourceId($resourceId = null) 
	{
		if ($resourceId !== null) {
			$this->resourceId = (int) $resourceId;
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
	 * Get or set the lifetime, if seconds, of the resource
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
}