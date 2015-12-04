<?php
namespace Mail;

use Bliss\ResourceComponent;

class Message extends ResourceComponent
{
	const RESOURCE_NAME = "mail-message";
	const GROUP_FROM = "from";
	const GROUP_TO = "to";
	const GROUP_CC = "cc";
	const GROUP_BCC = "bcc";
	const GROUP_REPLY_TO = "replyTo";
	
	/**
	 * @var string 
	 */
	protected $uid;
	
	/**
	 * @var string
	 */
	protected $parentUid;
	
	/**
	 * @var string
	 */
	protected $subject;
	
	/**
	 * @var MessageBody
	 */
	protected $body;
	
	/**
	 * @var int
	 */
	protected $size;
	
	/**
	 * @var array
	 */
	private $addresses = [
		self::GROUP_FROM		=> [],
		self::GROUP_TO			=> [],
		self::GROUP_CC			=> [],
		self::GROUP_BCC			=> [],
		self::GROUP_REPLY_TO	=> []
	];
	
	/**
	 * Constructor
	 * 
	 * @param string $uid The unique message ID
	 */
	public function __construct($uid = null)
	{
		$this->body = new MessageBody();
		$this->uid($uid);
	}
	
	/**
	 * @var string
	 */
	public function getResourceName() { return self::RESOURCE_NAME; }
	
	/**
	 * Get or set the message's unique ID
	 * 
	 * @param string $uid
	 * @return string
	 */
	public function uid($uid = null)
	{
		return $this->getSet("uid", $uid, self::VALUE_STRING);
	}
	
	/**
	 * Get or set the UID of the message this message is in reply to
	 * 
	 * @param string $uid
	 * @return string
	 */
	public function parentUid($uid = null)
	{
		return $this->getSet("parentUid", $uid, self::VALUE_STRING);
	}
	
	/**
	 * Get or set the message's subject line
	 * 
	 * @param string $subject
	 * @return string
	 */
	public function subject($subject = null)
	{
		return $this->getSet("subject", $subject, self::VALUE_STRING);
	}
	
	/**
	 * Get or set the email addresses the message is from
	 * 
	 * @param array $addresses
	 * @return array
	 */
	public function from(array $addresses = null)
	{
		return $this->getSetAddresses(self::GROUP_FROM, $addresses);
	}
	
	/**
	 * Get or set the email addresses the message is sent to
	 * 
	 * @param array $addresses
	 * @return array
	 */
	public function to(array $addresses = null)
	{
		return $this->getSetAddresses(self::GROUP_TO, $addresses);
	}
	
	/**
	 * Get or set the email addresses the message will be CC'd to
	 * 
	 * @param array $addresses
	 * @return array
	 */
	public function cc(array $addresses = null)
	{
		return $this->getSetAddresses(self::GROUP_CC, $addresses);
	}
	
	/**
	 * Get or set the email addresses the message will be BCC'd to
	 * 
	 * @param array $addresses
	 * @return string
	 */
	public function bcc(array $addresses = null)
	{
		return $this->getSetAddresses(self::GROUP_BCC, $addresses);
	}
	
	/**
	 * Get or set the email addresses to be replied to
	 * 
	 * @param array $addresses
	 * @return array
	 */
	public function replyTo(array $addresses = null)
	{
		return $this->getSetAddresses(self::GROUP_REPLY_TO, $addresses);
	}
	
	/**
	 * Get or set the email addresses for a particular group
	 * 
	 * @param string $group Name of the group.  These are defined in the class constants GROUP_*
	 * @param array $addresses Array of email address strings, formatted as "Personal Name <mailbox@host>"
	 * @return array
	 * @throws \Exception
	 */
	private function getSetAddresses($group, array $addresses = null)
	{
		if (!isset($this->addresses[$group])) {
			throw new \Exception("Invalid email address group: {$group}");
		}
		
		if ($addresses !== null) {
			$this->addresses[$group] = $addresses;
		}
		
		return $this->addresses[$group];
	}
	
	/**
	 * Get or set the size of the message (in bytes)
	 * 
	 * @param int $size
	 * @return int
	 */
	public function size($size = null)
	{
		return $this->getSet("size", $size, self::VALUE_INT);
	}
	
	/**
	 * Get or set the body of the message
	 * 
	 * @param MessageBody $body
	 * @return MessageBody
	 */
	public function body(MessageBody $body = null)
	{
		return $this->getSet("body", $body);
	}
	
	/**
	 * Override the default toArray method to add the email addresses to the exported array
	 * 
	 * @return array
	 */
	public function toArray() 
	{
		$data = parent::toArray();
		foreach ($this->addresses as $group => $addresses) {
			$data[$group] = $addresses;
		}
		return $data;
	}
}