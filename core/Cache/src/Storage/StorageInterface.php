<?php
namespace Cache\Storage;

interface StorageInterface
{
	public function get($hash, \DateTime $expires = null);
	
	public function delete($hash);
	
	public function put($hash, $contents);
}