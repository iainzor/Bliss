<?php
namespace View\Decorator;

use View\Partial;

class PartialWrapper implements DecoratorInterface
{
	/**
	 * @var string
	 */
	private $filename;
	
	/**
	 * Constructor
	 * 
	 * @param string $filename
	 */
	public function __construct($filename)
	{
		$this->filename = $filename;
	}
	
	/**
	 * Wrap the contents in with a view partial
	 * 
	 * @param string $contents
	 * @param array $params
	 * @return string
	 */
	public function decorate($contents, array $params = [])
	{
		$partial = new Partial($this->filename);
		$partial->setContents($contents);
		
		return $partial->render($params);
	}
}