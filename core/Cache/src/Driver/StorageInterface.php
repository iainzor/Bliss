<?php
namespace Cache\Driver;

use Cache\Resource\ResourceInterface;

interface StorageInterface
{
	public function get($hash);
	
	public function delete($hash);
	
	public function put($hash, ResourceInterface $resource);
}