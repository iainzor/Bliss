<?php
namespace Http;

class Request
{	
	/**
	 * @var string
	 */
	private $uri = "";
	
	/**
	 * @var Format\FormatInterface
	 */
	private $format;
	
	/**
	 * @var Format\FormatInterface
	 */
	private $defaultFormat;
	
	/**
	 * Initialize the request
	 * 
	 * @param \Http\Application $app
	 */
	public function init(Application $app)
	{
		$this->uri = $this->_findUri();
		$this->format = $this->_findFormat($app, $this->uri);
	}
	
	/**
	 * Get or set the request's default format.  If no default format is specified,
	 * an instance of \Http\Format\PlainTextFormat is used.
	 * 
	 * @param \Http\Format\FormatInterface $format
	 * @return \Http\Format\FormatInterface
	 */
	public function defaultFormat(Format\FormatInterface $format = null) : Format\FormatInterface
	{
		if ($format !== null) {
			$this->defaultFormat = $format;
		}
		if (!$this->defaultFormat) {
			$this->defaultFormat = new Format\PlainTextFormat();
		}
		return $this->defaultFormat;
	}
	
	/**
	 * Get the requested URI
	 * 
	 * @return string
	 */
	public function uri() : string
	{
		return $this->uri;
	}
	
	/**
	 * Get the requested format
	 * 
	 * @return \Http\Format\FormatInterface
	 */
	public function format() : Format\FormatInterface
	{
		return $this->format;
	}
	
	/**
	 * Return the value of a GET input variable
	 * 
	 * @see filter_input()
	 * @param string $name
	 * @param int $filter
	 * @param array $options
	 * @return mixed
	 */
	public function inputGet(string $name, int $filter = FILTER_DEFAULT, array $options = [])
	{
		return filter_input(INPUT_GET, $name, $filter, $options);
	}
	
	/**
	 * Find the requested URI that was requested
	 * 
	 * @return string
	 */
	private function _findUri() : string
	{
		$scriptName = filter_input(INPUT_SERVER, "SCRIPT_NAME");
		$requestUri = preg_replace("/^\/?([^\?]+)?.*$/i", "\\1", filter_input(INPUT_SERVER, "REQUEST_URI"));
		$quoted = preg_quote($scriptName, "/");
		$uri = preg_replace("/^". $quoted ."\/?(.*)$/i", "\\1", $requestUri);
		
		return trim($uri, "/");
	}
	
	/**
	 * Find the format for a specified uri
	 * 
	 * @param \Http\Application $app
	 * @param string $uri
	 * @return \Http\Format\FormatInterface
	 */
	private function _findFormat(Application $app, string $uri) : Format\FormatInterface
	{
		$registry = Format\FormatRegistry::generate($app, $this->defaultFormat());
		return $registry->determine($uri);
	}
}