<?php
namespace Mail\IMap;

use Mail\Message;

class Mailbox
{
	const SORT_ASC = 1;
	const SORT_DESC = 2;
	
	/**
	 * @var string
	 */
	private $path;
	
	/**
	 * @var string
	 */
	private $username;
	
	/**
	 * @var string
	 */
	private $password;
	
	/**
	 * Holds the stream resource for the IMAP server
	 * 
	 * @var resource
	 */
	private $stream;
	
	/**
	 * @var int
	 */
	private $sortDir = self::SORT_ASC;
	
	/**
	 * Constructor
	 * 
	 * @param string $path
	 * @param string $username
	 * @param string $password
	 */
	public function __construct($path, $username, $password)
	{
		$this->path = $path;
		$this->username = $username;
		$this->password = $password;
	}
	
	/**
	 * Destructor
	 * Closes the connection to the IMAP server
	 */
	public function __destruct() 
	{
		if (isset($this->stream)) {
			imap_close($this->stream);
		}
	}
	
	/**
	 * Get, or attempt to create, the stream resource to the IMAP server
	 * 
	 * @return resource
	 * @throws \Exception
	 */
	protected function stream()
	{
		if (!isset($this->stream)) {
			$this->stream = imap_open($this->path, $this->username, $this->password);
			
			if ($this->stream === false) {
				$errors = imap_errors();
				$error = count($errors) ? array_shift($errors) : "Unknown error";
				
				throw new \Exception("IMAP Connection Error: {$error}");
			}
		}
		
		return $this->stream;
	}
	
	/**
	 * Search the mailbox and return an array of messages found
	 * 
	 * @param string $query
	 * @return Message[]
	 */
	public function search($query = "ALL")
	{
		$ids = imap_search($this->stream(), $query);
		$messages = [];
		$factory = new MessageFactory($this);
		
		if ($ids) {
			foreach ($ids as $id) {
				$messages[] = $factory->create($id);
			}
		}
		
		usort($messages, [$this, "sort"]);
		
		return $messages;
	}
	
	/**
	 * Message used to sort messages according to the mailbox ordering
	 * 
	 * @param Message $a
	 * @param Message $b
	 * @return int
	 */
	public function sort(Message $a, Message $b)
	{
		$up = $this->sortDir === self::SORT_ASC ? 1 : -1;
		$down = $this->sortDir === self::SORT_ASC ? -1 : 1;
		
		if ($a->created() === $b->created()) {
			return 0;
		}
		return $a->created() > $b->created() ? $up : $down;
	}
	
	/**
	 * Get the headers for a single message
	 * 
	 * @param int $messageNumber
	 * @return object
	 */
	public function messageHeaders($messageNumber)
	{
		return imap_headerinfo($this->stream(), $messageNumber);
	}
	
	/**
	 * Get the structure of a single message
	 * 
	 * @param int $messageNumber
	 * @return object
	 */
	public function messageStructure($messageNumber)
	{
		return imap_fetchstructure($this->stream(), $messageNumber);
	}
	
	/**
	 * Get a portion of a message's body
	 * 
	 * @param int $messageNumber
	 * @param string $partNumber
	 * @return string
	 */
	public function bodyPart($messageNumber, $partNumber)
	{
		return imap_fetchbody($this->stream(), $messageNumber, $partNumber);
	}
	
	/**
	 * Get the entire contents of the message's body
	 * 
	 * @param int $messageNumber
	 * @return string
	 */
	public function body($messageNumber)
	{
		return imap_body($this->stream(), $messageNumber);
	}
}
