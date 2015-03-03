<?php
namespace Response\Format;

class GenericFormat implements FormatInterface
{
	/**
	 * @var string
	 */
	private $mime;
	
	/**
	 * Constructor
	 * 
	 * @param string $extension
	 * @param string $mime
	 */
	public function __construct($extension, $mime)
	{
		$this->extension = $extension;
		$this->mime = $mime;
	}
	
	/**
	 * @return string
	 */
	public function mime() 
	{ 
		return $this->mime; 
	}

	public function requiresView() { return false; }

	public function transform(\Response\Module $response) 
	{
		$params = $response->params();
		
		if ($response->body() === null && empty($params)) {
			throw new \Exception("Nothing to transform!");
		}
	}
}