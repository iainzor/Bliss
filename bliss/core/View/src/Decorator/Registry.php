<?php
namespace View\Decorator;

use Response\Format\FormatInterface;

class Registry
{
	/**
	 * @var \View\Decorator\DecoratorInterface[]
	 */
	private $decorators = [];
	
	/**
	 * Add a decorator to the registry
	 * 
	 * If a response format is provided, the decorator will only run when that 
	 * format is requested
	 * 
	 * @param \View\Decorator\DecoratorInterface $decorator
	 * @param \Response\Format\FormatInterface $format
	 */
	public function add(DecoratorInterface $decorator, FormatInterface $format = null)
	{
		$this->decorators[] = [
			"decorator" => $decorator,
			"format" => $format
		];
	}
	
	/**
	 * Get all decorators for a single response format
	 * 
	 * @param \Response\Format\FormatInterface $format
	 * @return \View\Decorator\DecoratorInterface[]
	 */
	public function belongingTo(FormatInterface $format)
	{
		$found = [];
		
		foreach ($this->decorators as $decorator) {
			if ($decorator["format"] === $format) {
				$found[] = $decorator["decorator"];
			}
		}
		
		return $found;
	}
}