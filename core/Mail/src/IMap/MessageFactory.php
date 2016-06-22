<?php
namespace Mail\IMap;

use Mail\Message,
	Mail\MessageBody;

class MessageFactory
{
	/**
	 * @var Mailbox
	 */
	private $mailbox;
	
	/**
	 * Constructor
	 * 
	 * @param \Mail\IMap\Mailbox $mailbox
	 */
	public function __construct(Mailbox $mailbox)
	{
		$this->mailbox = $mailbox;
	}
	
	/**
	 * Create a new message from a message number
	 * 
	 * @param int $messageNumber
	 * @return Message
	 */
	public function create($messageNumber)
	{
		$headers = $this->mailbox->messageHeaders($messageNumber);
		$date = new \DateTime($headers->date);
		$message = new Message($headers->message_id);
		$message->parentUid(!empty($headers->in_reply_to) ? $headers->in_reply_to : null);
		$message->subject($headers->subject);
		$message->created($date->getTimestamp());
		$message->size($headers->Size);
		
		if (!empty($headers->references)) {
			preg_match_all("/(<[^>]+>)/", $headers->references, $matches);
			$message->references($matches[1]);
		}
		
		$this->populateIdentities($message, $headers);
		$this->populateBody($message, $messageNumber);
		
		return $message;
	}
	
	/**
	 * Generate a list of email address from an array of identities
	 * 
	 * @param array $identities
	 * @return string[] An array of strings formatted as "Identity Name <mailbox@host.com>"
	 */
	public function generateEmailList(array $identities)
	{
		$emails = [];
		foreach ($identities as $item) {
			$parts = [];
			if (!empty($item->personal)) {
				$parts[] = $item->personal;
			}
			$parts[] = "<{$item->mailbox}@{$item->host}>";
			$emails[] = implode(" ", $parts);
		}
		
		return $emails;
	}
	
	/**
	 * Populate a message with the various identities (FROM, TO, CC, BCC)
	 * 
	 * @param Message $message
	 * @param object $headers
	 */
	public function populateIdentities(Message $message, $headers)
	{
		$sections = ["from", "to", "cc", "bcc", "reply_to" => "replyTo"];
		foreach ($sections as $method => $section) {
			if (is_numeric($method)) {
				$method = $section;
			}
			
			if (!empty($headers->{$section})) {
				$list = $this->generateEmailList($headers->{$section});
				call_user_func([$message, $method], $list);
			}
		}
	}
	
	/**
	 * Populate the body content of a message
	 * 
	 * @param Message $message
	 * @param int $messageNumber
	 */
	public function populateBody(Message $message, $messageNumber)
	{
		$structure = $this->mailbox->messageStructure($messageNumber);
		$body = $message->body();
		
		if (empty($structure->parts)) {
			$this->populateBodyPart($messageNumber, $body, $structure, 0);
		} else {
			foreach ($structure->parts as $i => $part) {
				$this->populateBodyPart($messageNumber, $body, $part, $i+1);
			}
		}
	}
	
	/**
	 * Populate a message's body part
	 * 
	 * @param int $messageNumber
	 * @param MessageBody $body
	 * @param object $part
	 * @param int $partNumber
	 */
	public function populateBodyPart($messageNumber, MessageBody $body, $part, $partNumber)
	{
		$data = $partNumber ? $this->mailbox->bodyPart($messageNumber, $partNumber) : $this->mailbox->body($messageNumber);
		$params = $this->generateBodyPartParams($part);
		
		switch ($part->encoding) {
			case ENCQUOTEDPRINTABLE:
				$data = quoted_printable_decode($data);
				break;
			case ENCBASE64:
				$data = base64_decode($data);
				break;
		}
		
		if (isset($params["filename"]) || isset($params["name"])) {
			// TODO - Attachments
		} else if (!empty($data)) {
			switch ($part->type) {
				case TYPETEXT:
					$data = mb_convert_encoding($data, "UTF-8", $params["charset"]);
					
					if (strtolower($part->subtype) === "plain") {
						$body->appendPlain($data);
					} else {
						$body->appendHtml($data);
					}
					break;
				case TYPEMESSAGE:
					$body->appendPlain($data);
					break;
			}
		}
		
		if (!empty($part->parts)) {
			foreach ($part->parts as $i => $subPart) {
				$subPartNumber = $partNumber .".". ($i+1);
				$this->populateBodyPart($messageNumber, $body, $subPart, $subPartNumber);
			}
		}
	}
	
	/**
	 * Generate an array of parameters for a body part
	 * 
	 * @param object $part
	 * @return array
	 */
	public function generateBodyPartParams($part)
	{
		$params = [];
		
		if (!empty($part->parameters)) {
			foreach ($part->parameters as $param) {
				$params[strtolower($param->attribute)] = $param->value;
			}
		}
		
		if (!empty($part->dparameters)) {
			foreach ($part->dparameters as $param) {
				$params[strtolower($param->attribute)] = $param->value;
			}
		}
		
		return $params;
	}
}