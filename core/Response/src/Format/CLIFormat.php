<?php
namespace Response\Format;

class CLIFormat implements FormatInterface
{
	public function mime() { return null; }

	public function requiresView() { return false; }

	public function transform(\Response\Module $response) 
	{
		echo $response->body();
		exit;
	}

}