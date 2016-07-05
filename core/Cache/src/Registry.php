<?php
namespace Cache;

use Bliss\Component;

class Registry
{
	/**
	 * @var Storage\StorageInterface
	 */
	private $storage;
	
	/**
	 * @var Resource\ResourceInterface
	 */
	private $resourceTemplate;
	
	/**
	 * @var array
	 */
	private $hashes = [];
	
	/**
	 * Constructor
	 * 
	 * @param \Cache\Storage\StorageInterface $storage
	 */
	public function __construct(Driver\StorageInterface $storage)
	{
		$this->storage = $storage;
		$this->resourceTemplate = new Resource\Resource($this);
	}
	
	/**
	 * Create a new cache resource
	 * 
	 * @param string $resourceName
	 * @param string $resourceId
	 * @param array $params
	 * @param mixed $contents
	 * @return Resource\ResourceInterface
	 */
	public function create($resourceName, $resourceId = null, array $params = [], $contents = null)
	{
		return $this->generateResource([
			"resourceName" => $resourceName,
			"resourceId" => $resourceId,
			"params" => $params,
			"contents" => $contents
		]);
	}
	
	/**
	 * Attempt to find a cache resource matching the arguments
	 * If a resource cannot be found it will be created, added to the registry and returned
	 * 
	 * @param string $resourceName
	 * @param int $resourceId
	 * @param array $params
	 * @return Resource\ResourceInterface
	 */
	public function get($resourceName, $resourceId = null, array $params = [])
	{
		$hash = $this->generateHash($resourceName, $resourceId, $params);
		$resource = $this->storage->get($hash);
		
		return $resource;
	}
	
	/**
	 * Save a resource's cache to the registry's storage
	 * 
	 * @param \Cache\Resource\ResourceInterface $resource
	 * @param \DateTime $expires The date and time the cache resource expires
	 * @return boolean TRUE on success, FALSE on failure
	 */
	public function put(Resource\ResourceInterface $resource)
	{
		$hash = $this->generateHash($resource->resourceName(), $resource->resourceId(), $resource->params());
		$res = $this->storage->put($hash, $resource);
		$this->hashes[$hash] = $hash;
		
		return $res;
	}
	
	/**
	 * Check if a resource exists in the cache
	 * 
	 * @param string $resourceName
	 * @param int $resourceId
	 * @param array $params
	 * @return boolean
	 */
	public function exists($resourceName, $resourceId = null, array $params = [])
	{
		$hash = $this->generateHash($resourceName, $resourceId, $params);
		$data = $this->storage->get($hash);
		
		return $data !== false;
	}
	
	/**
	 * Delete a cached resource and all it's children resources
	 * 
	 * @param \Cache\Resource\ResourceInterface $resource
	 */
	public function delete(Resource\ResourceInterface $resource)
	{
		$resourceName = $resource->resourceName();
		$resourceId = $resource->resourceId();
		$params = $resource->params();
		$hash = $this->generateHash($resourceName, $resourceId, $params);
		$hashParts = explode("-", $hash);
		
		$this->_delete($hash);
		
		if (!$resourceId || empty($params)) {
			$partialHash = !$resourceId 
				? $hashParts[0] 
				: $hashParts[0] ."-". $hashParts[1];
			
			foreach ($this->hashes as $hash) {
				if (preg_match("/^{$partialHash}/i", $hash)) {
					$this->_delete($hash);
				}
			}
		}
	}
	
	/**
	 * Delete cache from the storage and remove the hash from the registry
	 * 
	 * @param string $hash
	 */
	private function _delete($hash) 
	{
		$this->storage->delete($hash);
		unset($this->hashes[$hash]);
	}
	
	/**
	 * Generate a three part shortened MD5 hash for the passed arguments
	 * 
	 * @param string $resourceName
	 * @param int $resourceId
	 * @param array $params
	 * @return string
	 */
	private function generateHash($resourceName, $resourceId, array $params)
	{
		$parts = [$resourceName, $resourceId, json_encode($params)];
		
		return call_user_func(function() use ($parts) {
			$hashParts = [];
			foreach ($parts as $part) {
				$hash = substr(md5($part), 0, 10);
				$hashParts[] = $hash;
			}
			return implode("-", $hashParts);
		});
	}
	
	/**
	 * @param array $properties
	 * @return \Cache\Resource\ResourceInterface
	 */
	private function generateResource(array $properties)
	{
		$resource = clone $this->resourceTemplate;
		
		Component::populate($resource, $properties);
		
		return $resource;
	}
}