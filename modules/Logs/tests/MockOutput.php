<?php
use Logs\Output\OutputInterface;

class MockOutput implements OutputInterface
{
	public function next(\Logs\AbstractMessage $message) {}
}
