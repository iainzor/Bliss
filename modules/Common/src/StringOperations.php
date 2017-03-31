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
	
	/**
	 * Hyphenate a string by replacing all spaces and special characters with hyphens (-)
	 * If $maxLength is provided, the result will be truncated to this amount
	 * 
	 * @param string $str
	 * @param int $maxLength
	 * @return string
	 */
	public function hyphenate(string $str, int $maxLength = 0) : string
	{
		$str = preg_replace("/[^a-z0-9]+/i", " ", $str);
		$str = preg_replace("/\s+/", "-", $str);
		
		if ($maxLength > 0) {
			$str = $this->truncate($str, $maxLength);
		}
		
		return strtolower(trim($str, "-"));
	}
	
	/**
	 * Truncate a string to a specified length.  If an $append string is provided,
	 * it will be appended to the end of the string only if a truncation happens.
	 * 
	 * @param string $str
	 * @param int $length
	 * @param string $append
	 * @return string
	 */
	public function truncate(string $str, int $length, string $append = null) : string
	{
		if (strlen($str) > $length) {
			$str = substr($str, 0, $length);
			$str .= $append;
		}
		
		return $str;
	}
	
	/**
	 * Detect and convert a string to its correct value type
	 * 
	 * @param string $value
	 * @return mixed
	 */
	public function convertValueType(string $value) 
	{
		if (preg_match("/^[0-9]+\.[0-9]+$/", $value)) {
			return floatval($value);
		} else if (preg_match("/^[0-9]+$/", $value)) {
			return intval($value);
		}
		
		return $value;
	}
}
