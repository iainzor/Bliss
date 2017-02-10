<?php
namespace Logs\Output;

use Logs\Message\AbstractMessage;

class OutputDefinition
{
	/**
	 * @var OutputInterface
	 */
	private $output;
	
	/**
	 *
	 * @var callable 
	 */
	private $filter;
	
	/**
	 * Constructor
	 * 
	 * @param \Logs\Output\OutputInterface $output
	 * @param callable $filter
	 */
	public function __construct(OutputInterface $output, callable $filter = null)
	{
		$this->output = $output;
		$this->filter = $filter;
	}
	
	/**
	 * Send a message to the output instance.  If a filter is set, the message will
	 * be sent through it first to determine if the output can receive it.
	 * 
	 * @param AbstractMessage $message
	 */
	public function next(AbstractMessage $message)
	{
		$valid = true;
		if ($this->filter && is_callable($this->filter)) {
			$valid = (boolean) call_user_func($this->filter, $message);
		}
		
		if ($valid === true) {
			$this->output->next($message);
		}
	}
}