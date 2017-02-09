<?php
namespace Logs;

class Logger
{
	/**
	 * @var Output\OutputInterface
	 */
	private $output;
	
	/**
	 * @var Message[]
	 */
	private $messages = [];
	
	/**
	 * Constructor
	 * 
	 * @param \Logs\Output\OutputInterface $output
	 */
	public function __construct(Output\OutputInterface $output)
	{
		$this->output = $output;
	}
	
	/**
	 * Create and return a new LogMessage
	 * 
	 * @param string $text
	 * @return \Logs\LogMessage
	 */
	public function log(string $text) : LogMessage 
	{
		return $this->add(new LogMessage($text));
	}
	
	/**
	 * Create a return a new NoticeMessage
	 *  
	 * @param string $text
	 * @return \Logs\NoticeMessage
	 */
	public function notice(string $text) : NoticeMessage
	{
		return $this->add(new NoticeMessage($text));
	}
	
	/**
	 * 
	 * @param \Logs\AbstractMessage $message
	 * @return \Logs\AbstractMessage
	 */
	public function add(AbstractMessage $message) : AbstractMessage
	{
		$this->messages[] = $message;
		$this->output->next($message);
		
		return $message;
	}
}