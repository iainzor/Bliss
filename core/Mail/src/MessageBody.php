<?php
namespace Mail;

use Bliss\Component;

class MessageBody extends Component
{
	/**
	 * @var string
	 */
	protected $plain = "";
	
	/**
	 * @var string
	 */
	protected $html = "";
	
	/**
	 * @var string
	 */
	protected $charset;
	
	/**
	 * @var array
	 */
	protected $attachments = [];
	
	/**
	 * Get or set the plain text contents of the message body
	 * 
	 * @param string $contents
	 * @return string
	 */
	public function plain($contents = null)
	{
		return $this->getSet("plain", $contents);
	}
	
	/**
	 * Appened the string contents to the end of the plain text
	 * 
	 * @param string $contents
	 */
	public function appendPlain($contents)
	{
		if (strlen($this->plain)) {
			$this->plain .= "\n\n";
		}
		
		$this->plain .= $contents;
	}
	
	/**
	 * Get or set the HTML contents of the message body
	 * 
	 * @param string $contents
	 * @return string
	 */
	public function html($contents = null)
	{
		return $this->getSet("html", $contents);
	}
	
	/**
	 * Append the string contents to the end of the HTML text
	 * 
	 * @param string $contents
	 */
	public function appendHtml($contents)
	{
		if (strlen($this->html)) {
			$this->html .= "<br><br>";
		}
		
		$this->html .= $contents;
	}
	
	/**
	 * Get or set the message body charset
	 * 
	 * @param string $charset
	 * @return string
	 */
	public function charset($charset = null)
	{
		return $this->getSet("charset", $charset);
	}
}