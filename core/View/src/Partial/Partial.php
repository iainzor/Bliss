<?php
namespace View\Partial;

use Bliss\App\Container as App;

class Partial
{
	/**
	 * @var \Bliss\App\Container
	 */
	private $app;
	
	/**
	 * @var array
	 */
	protected $params = [];
	
	/**
	 * @var string
	 */
	protected $contents = null;
	
	/**
	 * @var string
	 */
	private $filename;
	
	/**
	 * Constructor
	 * 
	 * @param string $filename
	 * @param \Bliss\App\Container $app
	 */
	public function __construct($filename, App $app = null)
	{
		$this->app = $app;
		$this->filename = $filename;
	}
	
	/**
	 * Set an addition content string for the partial
	 * 
	 * @param string $contents
	 */
	public function setContents($contents)
	{
		$this->contents = (string) $contents;
	}
	
	/**
	 * Get the addition content string to render within the partial
	 * 
	 * @return string
	 */
	public function contents()
	{
		return $this->contents;
	}
	
	/**
	 * Get a view parameter
	 * 
	 * @param string $name
	 * @param mixed $defaultValue
	 * @return mixed
	 */
	public function get($name, $defaultValue = null)
	{
		return isset($this->params[$name])
			? $this->params[$name]
			: $defaultValue;
	}
	
	/**
	 * Render the view partial and return the output
	 * 
	 * @return string
	 * @throws \Exception
	 */
	public function render(array $params = [])
	{
		$this->params = $params;
		
		if (!is_file($this->filename)) {
			throw new \Exception("View partial couldn't be found: {$this->filename}");
		}
		
		ob_start();
		try {
			include $this->filename;
			return ob_get_clean();
		} catch (\Exception $e) {
			ob_end_clean();
			throw $e;
		}
	}
}