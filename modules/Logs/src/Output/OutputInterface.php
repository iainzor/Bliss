<?php
namespace Logs\Output;

use Logs\AbstractMessage;

interface OutputInterface
{
	public function next(AbstractMessage $message);
}