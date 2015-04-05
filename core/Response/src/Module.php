<?php
namespace Response;

class Module extends \Bliss\Module\AbstractModule implements Format\ProviderInterface
{
	/**
	 * @var array
	 */
	private $params = [];
	
	/**
	 * @var string
	 */
	private $body = null;
	
	/**
	 * @var int
	 */
	private $code = 200;
	
	/**
	 * @var \Response\Format\Registry
	 */
	private $formats;
	
	/**
	 * @var int
	 */
	private $lastModified;
	
	/**
	 * Add default formats to the response
	 * 
	 * @param \Response\Format\Registry $formats
	 */
	public function initResponseFormats(Format\Registry $formats) 
	{
		$formats
			->set("html", new Format\HtmlFormat())
			->set("json", new Format\JsonFormat());
	}
	
	/**
	 * Attempt to get a format by its extension
	 * 
	 * @param string $extension
	 * @return \Response\Format\FormatInterface
	 */
	public function format($extension = null)
	{
		if (!isset($this->formats)) {
			$this->_compileFormats();
		}
		
		return $this->formats->get($extension);
	}
	
	/**
	 * Get the response's default format
	 * 
	 * @return \Response\Format\DefaultFormat
	 */
	public function defaultFormat()
	{
		return $this->format();
	}
	
	/**
	 * Set the response code
	 * 
	 * @param int $code
	 */
	public function setCode($code)
	{
		$this->code = (int) $code;
	}
	
	/**
	 * @return array
	 */
	public function params()
	{
		return $this->params;
	}
	
	/**
	 * Set the response's parameters
	 * 
	 * @param array $params
	 */
	public function setParams(array $params)
	{
		$this->params = $params;
	}
	
	/**
	 * Set the response's body
	 * 
	 * @param string $body
	 */
	public function setBody($body)
	{
		$this->body = $body;
	}
	
	/**
	 * Get the current response body
	 * 
	 * @return string|null
	 */
	public function body()
	{
		return $this->body;
	}
	
	/**
	 * Add a header string to the response
	 * 
	 * @param string $string
	 */
	public function header($string)
	{
		header($string);
	}
	
	/**
	 * Enable or disable response caching headers
	 * 
	 * @param boolean $flag
	 */
	public function cache($flag = true)
	{
		if ($flag === true) {
			$this->header("Cache-Control: public");
		} else {
			$this->header("Cache-Control: no-store, no-cache, must-revalidate");
		}
	}
	
	/**
	 * Set when the response content was last modified
	 * 
	 * @param \DateTime $dateTime
	 */
	public function lastModified(\DateTime $dateTime)
	{
		$this->lastModified = $dateTime->getTimestamp();
		$this->header("Last-Modified: ". gmdate("D, d M Y H:i:s", $this->lastModified) ." GMT");
	}
	
	/**
	 * Check if the response content is expired
	 * 
	 * @return boolean
	 */
	public function isExpired()
	{
		if ($this->lastModified) {
			$modifiedHeader = filter_input(INPUT_SERVER, "HTTP_IF_MODIFIED_SINCE");
			$modified = strtotime($modifiedHeader) !== $this->lastModified;
			
			return $modified;
		}
		
		return true;
	}
	
	/**
	 * Sends a not modified header and exits the application
	 */
	public function notModified()
	{
		$protocol = filter_input(INPUT_SERVER, "SERVER_PROTOCOL");
		$this->header($protocol ." 304 Not Modified");
		exit;
	}
	
	/**
	 * Send the response's body
	 * If the body is empty, attempt to generate it
	 * 
	 * @param \Request\Module $request
	 * @return void
	 */
	public function send(\Request\Module $request)
	{
		$format = $this->format($request->getFormat());
		$view = $this->app->view();
		$protocol = filter_input(INPUT_SERVER, "SERVER_PROTOCOL");
		
		if (!isset($this->body)) {
			$this->body = $this->render($request);
		}
		
		try {
			$body = $view->decorate($this->body, $request->params(), $format);
		} catch (\Exception $e) {
			$body = "<pre>"
				  . "<h1>Error</h1>"
				  . "<h2>{$e->getMessage()}</h2>"
				  . $e->getTraceAsString()
				  . "</pre>";
		}
		
		$this->app->log("Sending response");
		
		header("Content-type: ". $format->mime());
		header("{$protocol} {$this->code}");
		
		echo $body;
	}
	
	/**
	 * Render the current request and return the generated string
	 * 
	 * @param \Request\Module $request
	 * @return string
	 */
	public function render(\Request\Module $request)
	{
		$this->app->log("Rendering request");
		$view = $this->app->view();
		
		try {
			$format = $this->format($request->getFormat());
		} catch (Format\InvalidFormatException $e) {
			$this->setCode(404);
			throw $e;
		}
		
		try {
			$body = $view->render($request, $this->params);
		} catch (\Exception $e) {
			if ($format->requiresView()) {
				throw $e;
			}
			$body = $format->transform($this); 
		}
		
		return $body;
	}
	
	/**
	 * Compile formats from all available modules
	 */
	private function _compileFormats()
	{
		$this->formats = new Format\Registry($this);
		
		foreach ($this->app->modules() as $module) {
			if ($module instanceof Format\ProviderInterface) {
				$module->initResponseFormats($this->formats);
			}
		}
	}
}