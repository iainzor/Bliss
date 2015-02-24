<?php
namespace View\Decorator;

use View\Partial\PartialInterface;

class PartialWrapper implements DecoratorInterface
{
	/**
	 * @var \View\Partial\PartialInterface
	 */
	private $partial;
	
	/**
	 * Constructor
	 * 
	 * @param \View\Partial\PartialInterface $partial
	 */
	public function __construct(PartialInterface $partial)
	{
		$this->partial = $partial;
	}
	
	/**
	 * Wrap the contents with a view partial
	 * 
	 * @param string $contents
	 * @param array $params
	 * @return string
	 */
	public function decorate($contents, array $params = [])
	{
		$this->partial->setContents($contents);
		
		return $this->partial->render($params);
	}
}