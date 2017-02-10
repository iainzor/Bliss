<?php
namespace Logs\Output;

use Logs\Message\AbstractMessage;

interface OutputInterface
{
	public function next(AbstractMessage $message);
}