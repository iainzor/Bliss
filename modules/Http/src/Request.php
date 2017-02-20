<?php
namespace Http;

class Request
{	
	/**
	 * @var string
	 */
	private $uri = null;
	
	/**
	 * Get the requested URI
	 * 
	 * @return string
	 */
	public function uri() : string
	{
		if ($this->uri === null) {
			$this->uri = $this->_findUri();
		}
		
		return $this->uri;
	}
	
	/**
	 * Find the requested URI that was requested
	 * 
	 * @return string
	 */
	private function _findUri() : string
	{
		$scriptName = filter_input(INPUT_SERVER, "SCRIPT_NAME");
		$requestUri = filter_input(INPUT_SERVER, "REQUEST_URI");
		$quoted = preg_quote($scriptName, "/");
		$uri = preg_replace("/^". $quoted ."\/?(.*)$/i", "\\1", $requestUri);
		
		return trim($uri, "/");
	}
}