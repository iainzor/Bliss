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
}