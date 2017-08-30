<?php
namespace Http;

class Request
{	
	/**
	 * @var string
	 */
	private $uri = "";
	
	/**
	 * @var Format\FormatRegistry
	 */
	private $formatRegistry;
	
	/**
	 * @var string
	 */
	private $body;
	
	/**
	 * Constructor
	 * 
	 * @param string $uri
	 */
	public function __construct(string $uri)
	{
		$this->uri = trim($uri, "/");
		$this->body = file_get_contents("php://input");
	}
	
	/**
	 * Set the format registry to be used by the request
	 * 
	 * @param \Http\Format\FormatRegistry $formatRegistry
	 */
	public function setFormatRegistry(Format\FormatRegistry $formatRegistry)
	{
		$this->formatRegistry = $formatRegistry;
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
	 * Set the request URI
	 * 
	 * @param string $uri
	 */
	public function setUri(string $uri)
	{
		$this->uri = $uri;
	}
	
	/**
	 * Get the requested format
	 * 
	 * @return \Http\Format\FormatInterface
	 */
	public function format() : Format\FormatInterface
	{
		return $this->formatRegistry->determine($this->uri);
	}
	
	/**
	 * Get the data from the body of the request.  This is useful for reading
	 * raw data from POST requests.
	 * 
	 * @return string
	 */
	public function body() : string
	{
		return $this->body;
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
	 * Return all GET values
	 * 
	 * @see filter_input_array()
	 * @param mixed $definition
	 * @param bool $addEmpty
	 * @return array
	 */
	public function inputGetAll($definition = null, bool $addEmpty = true) : array
	{
		return filter_input_array(INPUT_GET, $definition, $addEmpty) ?: [];
	}
	
	/**
	 * Return the value of a POST input variable
	 * 
	 * @param string $name
	 * @param int $filter
	 * @param array $options
	 * @return mixed
	 */
	public function inputPost(string $name, int $filter = FILTER_DEFAULT, array $options = [])
	{
		return filter_input(INPUT_POST, $name, $filter, $options);
	}
	
	/**
	 * Return all POST values
	 * 
	 * @see filter_input_array()
	 * @param mixed $definition
	 * @param bool $addEmpty
	 * @return array
	 */
	public function inputPostAll($definition = null, bool $addEmpty = true) : array
	{
		return filter_input_array(INPUT_POST, $definition, $addEmpty) ?: [];
	}
	
	/**
	 * Generate a new JsonRequest instance for the request
	 * 
	 * @return \Http\JsonRequest
	 */
	public function json() : JsonRequest
	{
		return new JsonRequest($this);
	}
	
	/**
	 * Get the request method
	 * 
	 * @return string
	 */
	public function method() : string
	{
		return filter_input(INPUT_SERVER, "REQUEST_METHOD");
	}
	
	/**
	 * Check if the request method is GET
	 * 
	 * @return bool
	 */
	public function methodIsGet() : bool
	{
		return $this->method() === "GET";
	}
	
	/**
	 * Check if the request method is POST
	 * 
	 * @return bool
	 */
	public function methodIsPost() : bool
	{
		return $this->method() === "POST";
	}
}