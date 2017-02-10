<?php
namespace Logs;

class Logger
{
	/**
	 * @var Message\AbstractMessage[]
	 */
	private $messages = [];
	
	/**
	 * @var Output\OutputDefinition[]
	 */
	private $outputs = [];
	
	/**
	 * Register a new output to send messages to.  If a callable filter is provided,
	 * it will be used to filter which messages should get through to the output instance. 
	 * 
	 * @param \Logs\Output\OutputInterface $output
	 * @param callable $filter
	 */
	public function registerOutput(Output\OutputInterface $output, callable $filter = null)
	{
		$def = new Output\OutputDefinition($output, $filter);
		foreach ($this->messages as $message) {
			$def->next($message);
		}
		$this->outputs[] = $def;
	}
	
	/**
	 * Create and return a new LogMessage
	 * 
	 * @param string $text
	 * @return Message\LogMessage
	 */
	public function log(string $text) : Message\LogMessage 
	{
		return $this->add(new Message\LogMessage($text));
	}
	
	/**
	 * Create a return a new NoticeMessage
	 *  
	 * @param string $text
	 * @return Message\NoticeMessage
	 */
	public function notice(string $text) : Message\NoticeMessage
	{
		return $this->add(new Message\NoticeMessage($text));
	}
	
	/**
	 * 
	 * @param Message\AbstractMessage $message
	 * @return Message\AbstractMessage
	 */
	public function add(Message\AbstractMessage $message) : Message\AbstractMessage
	{
		$this->messages[] = $message;
		
		foreach ($this->outputs as $output) {
			$output->next($message);
		}
		
		return $message;
	}
}