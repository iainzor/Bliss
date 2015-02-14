<?php
namespace Cache;

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
	public function __construct(Storage\StorageInterface $storage)
	{
		$this->storage = $storage;
		$this->resourceTemplate = new Resource\Resource();
	}
	
	/**
	 * Attempt to find a resource stored in the cache.  If one cannot be found
	 * it will be created, added to the cache and returned
	 * 
	 * @param string $resourceName
	 * @param int $resourceId
	 * @param array $params
	 * @return \Cache\Resource\ResourceInterface
	 */
	public function findOrCreate($resourceName, $resourceId = null, array $params = [])
	{
		$resource = $this->get($resourceName, $resourceId, $params);
		
		if ($resource === false) {
			$resource = $this->generateResource([
				"resourceName" => $resourceName,
				"resourceId" => $resourceId,
				"params" => $params
			]);
			$this->put($resource);
		}
		
		return $resource;
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
		$data = $this->storage->get($hash);
		
		if ($data !== false) {
			return $this->generateResource([
				"resourceName" => $resourceName,
				"resourceId" => $resourceId,
				"params" => $params,
				"contents" => $data
			]);
		}
		
		return false;
	}
	
	/**
	 * Save a resource's cache to the registry's storage
	 * 
	 * @param \Cache\Resource\ResourceInterface $resource
	 * @return \Cache\Resource\ResourceInterface
	 */
	public function put(Resource\ResourceInterface $resource)
	{
		$hash = $this->generateHash($resource->resourceName(), $resource->resourceId(), $resource->params());
		
		$this->storage->put($hash, $resource->contents());
		$this->hashes[$hash] = $hash;
		
		return $resource;
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
		$className = get_class($this->resourceTemplate);
		return $className::factory($properties);
	}
}