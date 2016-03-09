<?php
namespace Cache\Resource;

interface ResourceInterface extends \Bliss\Resource\ResourceInterface
{
	public function resourceName($resourceName = null);
	
	public function resourceId($resourceId = null);
	
	public function params(array $params = null);
	
	public function contents($contents = null);
	
	public function expires($expires = null);
	
	public function isExpired();
	
	public function save();
	
	public function load();
}