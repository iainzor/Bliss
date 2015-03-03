<?php
namespace Bliss;

class String
{
	/**
	 * Convert a string to camel case
	 *
	 * @param string $string
	 * @param boolean $ucFirst Should the first character be uppercase?
	 * @return string
	 */
	public static function toCamelCase($string, $ucFirst = true)
	{
		$string = str_replace(" ", "",
			ucwords(
				preg_replace("/[_\-\s\/+]/i", " ", $string)
			)
		);

		if ($ucFirst == false)
		{
			$string{0} = strtolower($string{0});
		}

		return $string;
	}

	/**
	 * Create a hyphenated string
	 * Replaces all non-alphanumeric characters with the $separator
	 *
	 * @param string $string
	 * @param string $separator
	 * @param boolean $lowercase Whether to convert the hyphenated string to lowercase
	 */
	public static function hyphenate($string, $separator = "-", $lowercase = true)
	{
		$newString = str_replace(array("'", '"'), "", $string);
		$newString = preg_replace("/[^a-z0-9]+/i", $separator, $newString);
		$chars = str_split($newString);
		$compiled = "";
		
		foreach ($chars as $i => $char) {
			if ($i > 0 && $char === ucwords($char)) {
				$compiled .= $separator;
			}
			$compiled .= $char;
		}
		
		if ($lowercase === true) {
			$compiled = strtolower($compiled);
		}
		
		$compiled = trim($compiled, "-");
		
		return $compiled;
	}
	
	/**
	 * Remove hyphens from a string, creating separate words
	 * 
	 * @param string $string
	 * @param string $separator
	 * @return string
	 */
	public static function unhyphenate($string, $separator = "-")
	{
		return str_replace($separator, " ", $string);
	}

	/**
	 * Generate a random string
	 *
	 * @param int $length
	 * @return string
	 */
	public static function random($length = 7)
	{
		$length = (int) $length;
		if ($length < 1) {
			$length = 1;
		}
		
		$chars = range(0, 9) + range("a", "z");
		$string = "";
		for ($i = 0; $i < $length; $i++)
		{
			$string .= $chars[array_rand($chars)];
		}

		return $string;
	}

	/**
	 * Truncate a string to the given length
	 *
	 * @param string $str
	 * @param int $length
	 * @param string $append
	 */
	public static function truncate($str, $length, $append = "...")
	{
		$length = (int) $length;
		if ($length == 0) {
			return $str;
		}

		if (strlen($str) > $length) {
			$str = substr($str, 0, $length) . $append;
		}

		return $str;
	}
	
	/**
	 * Properly format the sentences in a string
	 * 
	 * @param string $str
	 * @return string
	 */
	public static function formatSentences($str)
	{
		$str = str_replace("-", " ", $str);
		$puncuation = array(".", "?", "!");
		
		foreach ($puncuation as $p) {
			$parts = explode($p, $str);
			foreach ($parts as $i => $part) {
				$parts[$i] = ucfirst($part);
			}
			$str = implode("{$p} ", $parts);
		}
		
		return $str;
	}
	
	/**
	 * Convert a string representation into a number of bytes
	 * 
	 * @param string $str
	 * @return float
	 */
	public static function toBytes($str)
	{
		$kb = 1024;
		$mb = $kb*1024;
		$gb = $mb*1024;
		$tb = $gb*1024;
		
		if (preg_match("/^([0-9\.]+)[^a-z]*([a-z]+)$/is", $str, $matches)) {
			$f = (float) $matches[1];
			$d = strtolower($matches[2]);
			
			switch ($d) {
				case "tb":
					return $f*$tb;
				case "gb":
				case "gib":
					return $f*$gb;
				case "mb":
					return $f*$mb;
				case "kb":
					return $f*$kb;
				case "b":
				case "bytes":
					return $f;
			}
		}
	}
	
	/**
	 * Convert the number of bytes into a string representation
	 * 
	 * @param float $bytes
	 * @param int $decimalPlaces Number of decimal places to round off to
	 * @return string
	 */
	public static function fromBytes($bytes, $decimalPlaces = 2)
	{
		$bytes = (float) $bytes;
		$kb = 1024;
		$mb = $kb*1024;
		$gb = $mb*1024;
		$tb = $gb*1024;
		
		if ($bytes >= $tb) {
			return number_format($bytes/$tb, $decimalPlaces) ."TB";
		} elseif ($bytes >= $gb) {
			return number_format($bytes/$gb, $decimalPlaces). "GB";
		} elseif ($bytes >= $mb) {
			return number_format($bytes/$mb, $decimalPlaces). "MB";
		} elseif ($bytes >= $kb) {
			return number_format($bytes/$kb, $decimalPlaces). "KB";
		} else {
			return number_format($bytes). " Bytes";
		}
	}
}