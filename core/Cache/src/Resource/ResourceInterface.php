<?php
namespace Cache\Resource;

interface ResourceInterface extends \Bliss\Resource\ResourceInterface
{
	public function params(array $params = null);
	
	public function contents($contents = null);
}