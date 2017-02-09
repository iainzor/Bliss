<?php
use PHPUnit\Framework\TestCase;

use Logs\Logger,
	Logs\LogMessage;

class LoggerTest extends TestCase
{
	public function testLog()
	{
		$logger = new Logger(new MockOutput());
		$log = $logger->log("Test");
		
		$this->assertInstanceOf(LogMessage::class, $log);
	}
}
