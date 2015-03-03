<?php
namespace User\Hasher;

interface HasherInterface
{
	public function hash($value);
	
	public function matches($value, $hash);
}