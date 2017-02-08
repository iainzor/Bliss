<?php
namespace Common;

class StringOperations
{
	public function camelize($str, $ucFirst = true)
	{
		$clean = strtolower(preg_replace("/[^a-z0-9]+/i", " ", $str));
		$uppercase = ucwords($clean);
		$combined = preg_replace("/\s+/", "", $uppercase);
		
		if ($ucFirst !== true && strlen($combined) > 0) {
			$combined[0] = strtolower($combined[0]);
		}
		
		return $combined;
	}
}
