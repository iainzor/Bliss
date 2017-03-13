<?php
namespace Common;

class StringOperations
{
	/**
	 * Convert a string to camel case
	 * 
	 * @param string $str
	 * @param bool $ucFirst Whether to uppercase the first word
	 * @return string
	 */
	public function camelize(string $str, bool $ucFirst = true) : string
	{
		$clean = strtolower(preg_replace("/[^a-z0-9]+/i", " ", $str));
		$uppercase = ucwords($clean);
		$combined = preg_replace("/\s+/", "", $uppercase);
		
		if ($ucFirst !== true && strlen($combined) > 0) {
			$combined[0] = strtolower($combined[0]);
		}
		
		return $combined;
	}
	
	public function hyphenate(string $str, int $maxLength = 0) : string
	{
		$str = preg_replace("/[^a-z0-9]+/i", " ", $str);
		$str = preg_replace("/\s+/", "-", $str);
		
		if ($maxLength > 0) {
			$str = $this->truncate($str, $maxLength);
		}
		
		return strtolower(trim($str, "-"));
	}
	
	public function truncate(string $str, int $length, string $append = null) : string
	{
		if (strlen($str) > $length) {
			$str = substr($str, 0, $length);
			$str .= $append;
		}
		
		return $str;
	}
}
