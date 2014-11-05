<?php
namespace Response\Format;

class Registry
{
	/**
	 * @var \Response\Module
	 */
	private $response;
	
	/**
	 * @var \Response\Format\Format[]
	 */
	private $formats = [];
	
	/**
	 * @var \Response\Format\DefaultFormat
	 */
	private $defaultFormat;
	
	/**
	 * Constructor
	 * 
	 * @param \Response\Module $response
	 */
	public function __construct(\Response\Module $response)
	{
		$this->response = $response;
		$this->defaultFormat = new DefaultFormat();
	}
	
	/**
	 * Set a format for a given name
	 * 
	 * @param string $extension
	 * @param \Response\Format\FormatInterface $format
	 * @return \Response\Format\Registry
	 */
	public function set($extension, FormatInterface $format)
	{
		$this->formats[$extension] = $format;
		
		return $this;
	}
	
	/**
	 * Attempt to get a format instance by its extension
	 * 
	 * @param string $extension
	 * @return \Response\Format\FormatInterface
	 * @throws \Exception
	 */
	public function get($extension)
	{
		if (!isset($this->formats[$extension])) {
			$this->formats[$extension] = $this->_generate($extension);
		}
		
		return $this->formats[$extension];
	}
	
	/**
	 * Get the default format for the response
	 * 
	 * @return \Response\Format\DefaultFormat
	 */
	public function defaultFormat()
	{
		return $this->defaultFormat;
	}
	
	/**
	 * Generate a GenericFormat instance for a file extension
	 * 
	 * @param type $extension
	 * @return \Response\Format\GenericFormat
	 * @throws \Response\Format\InvalidFormatException
	 */
	private function _generate($extension)
	{
		$mimes = include $this->response->resolvePath("config/mimes.php");
		$mime = isset($mimes[$extension]) ? $mimes[$extension] : null;
		
		if ($mime === null) {
			throw new InvalidFormatException("Invalid extension: {$extension}");
		}
		$format = new GenericFormat($extension, $mime);
		
		unset($mimes);
		return $format;
	}
}