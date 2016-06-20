<?php
namespace Response\Format;

use Bliss\Component;

class CLIFormat implements FormatInterface
{
	public function mime() { return null; }

	public function requiresView() { return false; }

	public function transform(\Response\Module $response) 
	{}

}