<?php
namespace Cache\Driver;

interface StorageInterface
{
	public function get($hash);
	
	public function delete($hash);
	
	public function put($hash, $contents, $expires = null);
}