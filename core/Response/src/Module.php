<?php
namespace Response;

use Cache\Storage\StorageInterface as CacheStorage,
	Cache\Storage\FileStorage;

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
	 * @var \DateTime
	 */
	private $expires;
	
	/**
	 * @var boolean
	 */
	private $cache = false;
	
	/**
	 * @var \Cache\Storage\StorageInterface
	 */
	private $cacheStorage;
	
	public function init()
	{}
	
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
	 * @return boolean
	 */
	public function cache($flag = null)
	{
		$this->cache = (boolean) $flag;
		
		if ($this->cache === true) {
			$this->header("Cache-Control: public, max-age=7200");
			$this->header("Pragma: cache");
			
			if (!$this->isExpired()) {
				$this->notModified();
			} else {
				$this->checkCache($this->app->request());
			}
		} else {
			$this->header("Cache-Control: no-store, no-cache, must-revalidate");
		}
		
		return $this->cache;
	}
	
	/**
	 * Check if a request can be returned from cache 
	 * 
	 * @param \Request\Module $request
	 */
	private function checkCache(\Request\Module $request)
	{
		if ($this->cache === true && isset($this->expires)) {
			$storage = $this->cacheStorage();
			$hash = $this->cacheId($request->params());
			$cache = $storage->get($hash, $this->expires);
			
			if ($cache) {
				$this->_send($cache, $request);
				exit;
			}
		}
	}
	
	/**
	 * Save contents of a request to the response's cache
	 * If caching is disabled, nothing will happen
	 * 
	 * @param string $contents
	 * @param \Request\Module $request
	 */
	private function saveCache($contents, \Request\Module $request)
	{
		if ($this->cache === true) {
			$storage = $this->cacheStorage();
			$hash = $this->cacheId($request->params());
			$storage->put($hash, $contents);
		}
	}
	
	/**
	 * Generate a cache ID using a set of parameters
	 * 
	 * @param array $params
	 * @return string
	 */
	private function cacheId(array $params)
	{
		return md5(json_encode($params));
	}
	
	/**
	 * Get or set the storage used for caching requests
	 * 
	 * @param CacheStorage $storage
	 * @return CacheStorage
	 */
	public function cacheStorage(CacheStorage $storage = null)
	{
		if ($storage !== null) {
			$this->cacheStorage = $storage;
		}
		if (!isset($this->cacheStorage)) {
			$this->cacheStorage = new FileStorage($this->app->resolvePath("files/response"));
		}
		return $this->cacheStorage;
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
	 * Set when the response expires
	 * 
	 * @param \DateTime $dateTime
	 */
	public function expires(\DateTime $dateTime)
	{
		$this->expires = $dateTime;
		$this->header("Expires: ". gmdate("D, d M Y H:i:s", $dateTime->getTimestamp()) ." GMT");
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
		$this->app->quit();
	}
	
	/**
	 * Send the response's body
	 * If the body is empty, attempt to generate it
	 * 
	 * @param \Request\Module $request
	 */
	public function send(\Request\Module $request, \View\Module $view)
	{
		$format = $this->format($request->getFormat());
		
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
		
		$this->_send($body, $request);
	}
	
	/**
	 * Set headers for a request and output the contents
	 * 
	 * @param string $contents
	 * @param \Request\Module $request
	 */
	private function _send($contents, \Request\Module $request)
	{
		$this->app->log("Sending response");
		$this->saveCache($contents, $request);
		
		$protocol = filter_input(INPUT_SERVER, "SERVER_PROTOCOL");
		$format = $this->format($request->getFormat());
		
		header("Content-type: ". $format->mime());
		header("{$protocol} {$this->code}");
		
		echo $contents;
		exit;
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