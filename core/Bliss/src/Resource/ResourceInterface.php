<?php
namespace Bliss\Resource;

interface ResourceInterface
{
	public function resourceName($resourceName = null);
	
	public function resourceId($resourceId = null);
}