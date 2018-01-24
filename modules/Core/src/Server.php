<?php
namespace Core;

class Server
{
	/**
	 * Generate a complete URI for a provided path relative to the current host
	 * 
	 * @param string $path
	 * @return string
	 */
	public static function generateUri(string $path = null) : string
	{
		$scheme = filter_input(INPUT_SERVER, "HTTPS") === "on" ? "https" : "http";
		$host = filter_input(INPUT_SERVER, "HTTP_HOST");
		
		return sprintf("%s://%s/%s", $scheme, $host, $path);
	}
}